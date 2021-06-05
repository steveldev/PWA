const PREFIX = "v1";
const CACHE_FILES = [
    '/offline.html',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css'
];

self.addEventListener('install', (event) => {
    self.skipWaiting(); // force l'arrêt des process, installe et active le nouveau process
    event.waitUntil(
        (async () => { 
            // Stockage des données dans le cache
            const cache = await caches.open(PREFIX);
            return cache.addAll(CACHE_FILES);
        })()
    );
    console.log(`${PREFIX} install`);
});

self.addEventListener('activate', (event) => {
    clients.claim(); // force le controle de la page dès l'activation du service worker
    event.waitUntil(
        (async () => {
            // Supprime les anciennes clés du cache
            const keys = await caches.keys();
            await Promise.all(
                keys.map((key) => {
                    // si la clé est différente de la version actuelle (PREFIX), on la supprime
                    if(!key.includes(PREFIX)) { 
                        return caches.delete(key);
                    }
                })
            );   
        })()
    );
    console.log(`${PREFIX} Activate`);
});

self.addEventListener('fetch', event => {
    // note : activate preserve logs in dev tool
    console.log(`Fetching : ${event.request.url}, Mode : ${event.request.mode}`);
    // on capte les navigations utilisateur 'navigate'
    if(event.request.mode === 'navigate') {
        event.respondWith( 
            (async () => {
                try {
                    // online 

                        // recupère la version préloadée du navigateur
                        const preloadResponse = await event.preloadResponse;
                        if(preloadResponse) {
                            return preloadResponse;
                        }

                        // récupère la ressource par le réseau
                        return await fetch(event.request);

                } catch (e) {
                    // offline

                        // récupère le fichier offline.html depuis le cache
                        const cache = await caches.open(PREFIX);
                        return await cache.match('/offline.html');
                }    
            })()
        );        
    } else if( CACHE_FILES.includes(event.request.url)) {
        // Mise en cache des fichiers 
        event.respondWith(caches.match(event.request));
    }  

});