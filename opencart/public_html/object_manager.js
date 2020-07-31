ymaps.ready(init);

function init () {
    var myMap = new ymaps.Map('map', {
            center: [58.222939, 38.490642],
            zoom: 5
        }, {
            searchControlProvider: 'yandex#search'
        }),
        objectManager = new ymaps.ObjectManager({
            // Чтобы метки начали кластеризоваться, выставляем опцию.
            clusterize: true,
            // ObjectManager принимает те же опции, что и кластеризатор.
            gridSize: 32,
            clusterDisableClickZoom: true
        });

    // Чтобы задать опции одиночным объектам и кластерам,
    // обратимся к дочерним коллекциям ObjectManager.
    objectManager.objects.options.set('preset', 'islands#blueFactoryIcon');
    objectManager.clusters.options.set('preset', 'islands#blueClusterIcons');
    myMap.geoObjects.add(objectManager);

    $.ajax({
        url: "data.json"
    }).done(function(data) {
        objectManager.add(data);
    });

}