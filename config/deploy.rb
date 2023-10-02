set :application, 'hours-of-operation'
set :repo_url, 'https://github.com/UNC-Libraries/wplibcalhours.git'

set :linked_dirs, %w[vendor]

set :deploy_to, "/net/deploy/#{fetch(:stage)}/#{fetch(:application)}"

set :pty, true
