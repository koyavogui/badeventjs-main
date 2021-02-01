 
$(document).ready(function () {
    $("#pays").change(function (e) { 
        e.preventDefault();
         
        const selectedCountry = $(this).children("option:selected").val();
        
        console.log(selectedCountry);
        // const data = $("#pays").val;
        $.post("./controller/getcity.php", {selectedCountry : selectedCountry} ,
            function (r) {
                console.log(r);
                $("#ville").html(r);
            },
        );
    });

   

    $("#secondSearch").submit(function (e) { 
        e.preventDefault();
        console.log('e');
        const form = $(this);
        var formdata = new FormData(form[0]);
        function addData(chart, labels, data, label) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.data.datasets[0].label = label;
            chart.update();
        }
        $.ajax({
            type: "POST",
            url: "./controller/gettable.php",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (r) {
                  $("#result").html(r);
            },
        });
        $.ajax({
            type: "POST",
            url: "./controller/getchart.php",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (r) {
                //  console.log(r.data.length); 
                //  var obj = JSON.parse( r.donnees);
                  
                 var ctx = $('#mongraph');
                 if ( r.data.length == 0) {
                    $("#mongraph").hide();
                 }else{
                    $("#mongraph").show();
                    const label = $('caption').attr('data-label');
                     addData(monGraph, r.label, r.data, label );
                       
                    //  console.log(L.marker([0,0]).addTo(mymap));
                    //  L.marker([0,0]).addTo(mymap).on('dragend', onDragEnd);
                    //  console.log(L.setLatLng);
                 }
            },
            dataType: "JSON",
        });
        $.ajax({
            type: "POST",
            url: "./controller/getmap.php",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (d) {
                 console.log(d);
                     var position = [parseInt(d.lat), parseInt(d.lng)];
                     marker.setLatLng(position, {
                       draggable: 'true'
                     }).bindPopup(position).update();
                     mymap.panTo(position);
                     mymap.addLayer(marker);
            },
            dataType: "JSON",
        });

        // $.post("./controller/getmap.php", formdata,
        //     function (m) {
        //     },
        //     "json"
        // );
    }); 
});