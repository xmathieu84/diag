if (document.title =='Liste entreprises') {
    document.addEventListener('DOMContentLoaded', () => {
    fetch('/administrateur/marker', {
    method :'GET'
})
    .then(function (response) {
        return response.json();
    })
    .then(function(response){
        JSON.stringify(response);
       
        
        var script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyB7MwwRvn0FLxtVv6GRuUEo3QDgPDMQkWU&callback=initMap';
        script.defer = true;
        script.async = true;
        window.initMap = function (){
    
            map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 46.9, lng: 2.43896484375 },
            zoom: 6,
            mapTypeControl: true,
            scrollwheel: false,
                zoomControl: false,
            
            
            })
            for (let i = 0; i < response.length; i++) {
                var myLatlng = new google.maps.LatLng(response[i][0],response[i][1])
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    
                    map : map
                    
                })
                
            }
};
            document.head.appendChild(script);
    
})
})
}











      