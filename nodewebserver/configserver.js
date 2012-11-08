/**
 * Configuration:
 *
 * HOSTS: 
 * dev env: 10.135.7.199 
 * prod env fe01: sgt-ink01.singletons.bk.sapo.pt (10.135.8.31)
 * prod env fe02: sgt-ink02.singletons.bk.sapo.pt (10.135.8.32)
 * prod env VIP: sgt-ink-be-vip.singletons.bk.sapo.pt (10.135.8.30)
 *
 * PORT: 
 *  8081 
 */
var config = {
    host: '10.135.7.199',
    //host: 'sgt-ink01.singletons.bk.sapo.pt', // prod vm01
    //host: 'sgt-ink02.singletons.bk.sapo.pt', // prod vm02
    port: 8081
};

module.exports.configserver = config;
