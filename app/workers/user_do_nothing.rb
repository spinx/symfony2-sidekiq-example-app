class UserDoNothing
    include Sidekiq::Worker

    def perform(*arguments)
        cmd = "php app/console worker:exec --decode"
        args = Base64.encode64(gzip(arguments.to_json))

        logger.info "Calling #{cmd} dlabs.user.queue_handler.do_nothing '#{args}'"

        # call SF2 command and redirect stderr with 2>&1
        output=`#{cmd} dlabs.user.queue_handler.do_nothing '#{args}' 2>&1`; result=$?.success?

        if result != true
            raise output
        end
    end
end