require 'sinatra'
require 'sidekiq'
require 'redis'
require 'base64'
require 'zlib'
require 'stringio'

Dir[File.dirname(__FILE__) + '/app/workers/*.rb'].each {|file| require file }

Sidekiq.configure_server do |config|
  config.redis = {}
end

def gzip(string)
    wio = StringIO.new("w")
    w_gz = Zlib::GzipWriter.new(wio)
    w_gz.write(string)
    w_gz.close
    compressed = wio.string
end