<script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js') }}"></script>
 {{--  Bootstrap  --}}
<script src="{{ asset('assets/js/lib/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/bootstrap.min.js') }}"></script>
 {{--  Ionicons   --}}
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<!-- Owl Carousel -->
<script src="{{ asset('assets/js/plugins/owl-carousel/owl.carousel.min.js') }}"></script>
{{--   jQuery Circle Progress   --}}
<script src="{{ asset('assets/js/plugins/jquery-circle-progress/circle-progress.min.js') }}"></script>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="{{ asset('assets/js/lib/webcam.min.js') }}"></script>
<script src="{{ asset('admin/dashboard/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


{{--  <script src="webcam.min.js"></script>  --}}

<!-- Base Js File -->
<script src="{{ asset('assets/js/base.js') }}"></script>

{{--  <script>
    am4core.ready(function () {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create("chartdiv", am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        chart.legend = new am4charts.Legend();

        chart.data = [
            {
                country: "Hadir",
                litres: 501.9
            },
            {
                country: "Sakit",
                litres: 301.9
            },
            {
                country: "Izin",
                litres: 201.1
            },
            {
                country: "Terlambat",
                litres: 165.8
            },
        ];



        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "litres";
        series.dataFields.category = "country";
        series.alignLabels = false;
        series.labels.template.text = "{value.percent.formatNumber('#.0')}%";
        series.labels.template.radius = am4core.percent(-40);
        series.labels.template.fill = am4core.color("white");
        series.colors.list = [
            am4core.color("#1171ba"),
            am4core.color("#fca903"),
            am4core.color("#37db63"),
            am4core.color("#ba113b"),
        ];
    });
</script>  --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        am4core.ready(function () {
            var chart = am4core.create("chartdiv", am4charts.PieChart3D);
            chart.hiddenState.properties.opacity = 0; // animasi awal
            chart.legend = new am4charts.Legend();
            chart.data = [
                { country: "Hadir", litres: 501.9 },
                { country: "Sakit", litres: 301.9 },
                { country: "Izin", litres: 201.1 },
                { country: "Terlambat", litres: 165.8 }
            ];

            var series = chart.series.push(new am4charts.PieSeries3D());
            series.dataFields.value = "litres";
            series.dataFields.category = "country";
            series.alignLabels = false;
            series.labels.template.text = "{value.percent.formatNumber('#.0')}%";
            series.labels.template.radius = am4core.percent(-40);
            series.labels.template.fill = am4core.color("white");
            series.colors.list = [
                am4core.color("#1171ba"),
                am4core.color("#fca903"),
                am4core.color("#37db63"),
                am4core.color("#ba113b")
            ];
        });
    });

  document.addEventListener("DOMContentLoaded", function () {
    const splashScreen = document.getElementById("splashScreen");

    if (!sessionStorage.getItem("splashShown")) {
      splashScreen.style.display = "flex";
      setTimeout(function () {
        splashScreen.style.display = "none";
        sessionStorage.setItem("splashShown", "true");
      }, 2000);
    } else {
      splashScreen.style.display = "none";
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('toggleSidebar');

    toggleBtn.addEventListener('click', function () {
        sidebar.style.left = '0';
        overlay.style.display = 'block';
    });

    overlay.addEventListener('click', function () {
        sidebar.style.left = '-250px';
        overlay.style.display = 'none';
    });
});

 </script>


@stack('myscript')
