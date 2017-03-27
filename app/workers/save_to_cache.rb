class UserDoNothing
    include Sidekiq::Worker

    def perform(key, data, ttl)
        redis = Redis.new
        redis.set(key, data, ttl)
    end
end