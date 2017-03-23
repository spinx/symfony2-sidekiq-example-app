class UserTrackerProcess
    include Sidekiq::Worker

    def perform(*arguments)
        return true
    end
end