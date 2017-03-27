class UserDoNothing
    include Sidekiq::Worker

    def perform(key, data)
        redis = Redis.new
        redis.set(key, data)
    end
end