ENV['VAGRANT_DEFAULT_PROVIDER'] = 'docker'

Vagrant.configure("2") do |config|
  config.vm.define "vagrant-tester" do |a|
    a.vm.provider "docker" do |d|
      d.name = "vagrant-tester"
      d.image = "guilhem/vagrant-ubuntu"
      d.has_ssh = true
      d.remains_running = true
    end
    a.vm.synced_folder "tests/synced", "/synced"
  end
end
