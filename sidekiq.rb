require 'redis'
require 'redis-namespace'
require 'statsd-ruby'
require 'sidekiq'
require 'sidekiq/api'
require 'base64'
require 'zlib'
require 'stringio'
require 'sidekiq-statistic'
require 'sidekiq-limit_fetch'

Dir[File.dirname(__FILE__) + '/app/workers/*.rb'].each {|file| require file }

Sidekiq::Statistic.configure do |config|
    config.log_file = 'app/logs/sidekiq-statistic.log'
end

ENV['RAILS_ENV'] = ENV['RAILS_ENV'] != "development" ? ENV['RAILS_ENV'] : "devbox"


# Redis connection
if ENV['RAILS_ENV'] == 'production'
    rediscfg = { url: 'redis://127.0.0.1:6379/0' }
else
    rediscfg = { url: 'redis://127.0.0.1:6379/0' }
end


Sidekiq.configure_server do |config|
    config.redis = rediscfg
    config.average_scheduled_poll_interval = 1
end

Sidekiq.configure_client do |config|
    config.redis = rediscfg
end

def gzip(string)
    wio = StringIO.new("w")
    w_gz = Zlib::GzipWriter.new(wio)
    w_gz.write(string)
    w_gz.close
    compressed = wio.string
end