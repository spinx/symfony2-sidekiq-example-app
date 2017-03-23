require 'redis'
require 'redis-namespace'
require 'statsd-ruby'
require 'sidekiq'
require 'sidekiq/api'
require 'sidekiq-ent'
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

Sidekiq.configure_client do |config|
    config.redis = rediscfg
end

Sidekiq.configure_server do |config|
    config.redis = rediscfg
    config.super_fetch!
    config.reliable_scheduler!
    config.average_scheduled_poll_interval = 1

    # Sending metrics to DataDog
    METRICS = Statsd.new('127.0.0.1', 8125)
    config.server_middleware do |chain|
        require 'sidekiq/middleware/server/statsd'
        chain.add Sidekiq::Middleware::Server::Statsd, :client => METRICS
    end

    # Register cronjobs from yaml
    config.periodic do |mgr|
        crons_file = File.dirname(__FILE__) + '/app/config/crontab_'+ENV['RAILS_ENV']+'.yml'
        crons = File.file?(crons_file) ? YAML.load_file(crons_file) : []

        if crons && crons.any?
            crons.each { |job| 
                begin
                    mgr.register(job['cron'], job['job_class'], args: job['args'], queue: job['queue']) 
                rescue Exception
                    raise "\n\nProperties missing or wrong format in crontab.yml\n\n"
                end
            }
        end

    end
end

def gzip(string)
    wio = StringIO.new("w")
    w_gz = Zlib::GzipWriter.new(wio)
    w_gz.write(string)
    w_gz.close
    compressed = wio.string
end
