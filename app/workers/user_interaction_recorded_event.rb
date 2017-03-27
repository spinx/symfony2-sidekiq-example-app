class UserInteractionRecordedEvent
    include Sidekiq::Worker

    def perform(*arguments)
        cmd = "php bin/console worker:exec --decode -e #{ENV['RAILS_ENV']} --no-debug"
        args = Base64.encode64(gzip(arguments.to_json))

        logger.info "Calling #{cmd} dlabs.user.event.interaction_recorded.event_handler '#{args}'"

        # call SF2 command and redirect stderr with 2>&1
        output=`#{cmd} dlabs.user.event.interaction_recorded.event_handler '#{args}' 2>&1`; result=$?.success?

        if result != true
            raise output
        end
    end
end