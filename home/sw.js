const CACHE_NAME = 'mekeng-v1';
const urlsToCache = [
    './img/main_pro_img02.png',
    './img/main_pro_img03.png',
    './img/main_pro_img04.png',
    './img/pro_img01.png',
    './img/pro_img02.png',
    './img/vi_img01.png',
    './img/vi_bg01.png'
];

// Service Worker 설치
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

// 캐시 및 네트워크 요청 처리
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                return fetch(event.request);
            })
    );
});

// 뒤로가기 버튼 이벤트 처리
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'BACK_BUTTON') {
        // 뒤로가기 버튼 이벤트 처리
        event.waitUntil(
            clients.matchAll().then(clients => {
                clients.forEach(client => {
                    client.postMessage({
                        type: 'BACK_BUTTON_PRESSED',
                        data: event.data
                    });
                });
            })
        );
    }
});