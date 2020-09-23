/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../../css/app.scss';
import * as dt_bs4 from 'datatables.net-bs4'
import * as fh_bs from 'datatables.net-fixedheader-bs4';
import * as r_bs from 'datatables.net-responsive-bs4';

require('@fortawesome/fontawesome-free/css/all.min.css');
require('datatables.net-dt/css/jquery.dataTables.min.css');
require('datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css');
require('datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css');

require('@fortawesome/fontawesome-free/js/all.js');

const $ = require('jquery');
global.$ = global.jQuery = $;
// import 'popper.js';
require('bootstrap');


document.addEventListener("DOMContentLoaded", function () {
    console.log("resource statistics!");

    const app_rest_admin_statisticsmonitoring_statisticslist = window
        .Routing.generate('app_rest_admin_statisticsmonitoring_statisticslist');

    updateSatisticsData();

    /**
     *
     */
    function updateSatisticsData() {
        $.ajax({
            type: "POST",
            url: app_rest_admin_statisticsmonitoring_statisticslist,

            error: (result) => {
                const body = $('body');
                let resource_statistics = body.find('#resource_statistics');
                resource_statistics.empty();

                setTimeout(function () {
                    updateSatisticsData();
                }, 5000);
            },
            success: (data) => {
                const body = $('body');
                let resource_statistics = body.find('#resource_statistics');
                resource_statistics.empty();
                resource_statistics.append(data.data);
                setTimeout(function () {
                    updateSatisticsData();
                }, 20000);
            }
        })
    }
});