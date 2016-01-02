(function() {
  'use strict';

  window.onload = function() {
    /**
     *
     * @constructor
     */
    var GA = function() {
      this.$properties = document.getElementById('js-wpkpidb-properties');

      this.ajaxUrl = '';

      this.setEvents();
    };

    GA.prototype.setEvents = function() {
      var self = this;
      var $select = document.getElementById('js-wpkpidb-account-select');
      var $propertySelect = document.getElementById('js-wpkpidb-property-select');

      if($select) {
        $select.addEventListener('change', function(e) {
          self.properties(e.target);
        });
      }
      if($propertySelect) {
        $propertySelect.addEventListener('change', function(e) {
          self.profiles(e.target);
        });
      }
    };

    GA.prototype.properties = function(target) {
      var xmlHttpRequest = new XMLHttpRequest();
      var select = document.createElement('select');
      var account = target.value;
      var self = this;

      xmlHttpRequest.addEventListener('loadend', function() {
        if(xmlHttpRequest.status === 200) {
          var response = xmlHttpRequest.response;
          var $properties = document.getElementById('js-wpkpidb-properties');

          select.name = 'ga_property';
          select.id = 'js-wpkpidb-property-select';
          select.innerHTML = response;
          if($properties.childNodes.length) {
            while($properties.firstChild) {
              $properties.removeChild($properties.firstChild);
            }
          }
          $properties.appendChild(select);

          self.profiles();
          self.setEvents();
        }
      });
      xmlHttpRequest.open('POST', this.ajaxUrl, true);
      xmlHttpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xmlHttpRequest.send('ajax_ga_account=' + account);
    };

    GA.prototype.profiles = function() {
      var xmlHttpRequest = new XMLHttpRequest();
      var select = document.createElement('select');
      var account = document.getElementById('js-wpkpidb-account-select').value;
      var property = document.getElementById('js-wpkpidb-property-select').value;
      var self = this;

      xmlHttpRequest.addEventListener('loadend', function() {
        if(xmlHttpRequest.status === 200) {
          var response = xmlHttpRequest.response;
          var $profiles = document.getElementById('js-wpkpidb-profiles');

          select.name = 'ga_profile';
          select.innerHTML = response;
          if($profiles.childNodes.length) {
            while($profiles.firstChild) {
              $profiles.removeChild($profiles.firstChild);
            }
          }
          $profiles.appendChild(select);

          self.setEvents();
        }
      });
      xmlHttpRequest.open('POST', this.ajaxUrl, true);
      xmlHttpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xmlHttpRequest.send('ajax_ga_account=' + account + '&ajax_ga_property=' + property);
    };

    new GA();


    /**
     *
     * @returns {boolean}
     * @constructor
     */
    var Years = function() {
      this.$select = document.getElementById('js-wpkpidb-years-select');
      if(!this.$select)
        return false;

      this.setEvents();
      this.changeTable(this.$select);
    };

    Years.prototype.setEvents = function() {
      var self = this;
      this.$select.addEventListener('change', function(e) {
        self.changeTable(e.target);
      });
    };

    Years.prototype.changeTable = function(target) {
      var year = target.options[target.selectedIndex].value;
      var tables = document.querySelectorAll('.js-wpkpidb-years-table');

      _.forEach(tables, function(table) {
        if(table.id === 'js-wpkpidb-years-table-' + year) {
          table.style.display = 'block';
        } else {
          table.style.display = 'none';
        }
      });
    };

    new Years();


    /**
     *
     * @returns {boolean}
     * @constructor
     */
    var DashboardForm = function() {
      this.$form = document.getElementById('js-wpkpidb-db-form');
      this.$select = document.getElementById('js-wpkpidb-db-period-select');
      if(!this.$form || !this.$select)
        return false;

      this.setEvents();
    };

    DashboardForm.prototype.setEvents = function() {
      var self = this;
      this.$select.addEventListener('change', function(e) {
        self.submit();
      });
    };

    DashboardForm.prototype.submit = function() {
      this.$form.submit();
    };

    new DashboardForm();


    var DashboardGraph = function() {
      this.$graphs = document.querySelectorAll('.js-wpkpidb-donut-graph');
      this.width = 160;
      this.height = 160;
      this.radius = Math.min(this.width, this.height) / 2;

      this.color = d3.scale.ordinal()
        .range(['#0085ba', '#ccc']);


      this.draw();
    };

    DashboardGraph.prototype.draw = function() {
      var self = this;
      _.forEach(this.$graphs, function($graph) {
        var kpi = $graph.dataset.kpi;
        var value = $graph.dataset.value;
        var rest = Math.max(kpi - value, 0);
        var data = [ value, rest ];
        var color = [ '#0085ba', '#eeeeee' ];

        var arc = d3.svg.arc()
          .outerRadius(self.radius - 10)
          .innerRadius(self.radius - 18);

        var pie = d3.layout.pie()
          .value(function(d) { return d; });

        var svg = d3.select('.js-wpkpidb-donut-graph').append('svg')
          .attr('width', self.width)
          .attr('height', self.height)
          .append('g')
          .attr('transform', 'translate(' + self.width / 2 + ',' + self.height / 2 + ')');

        var g = svg.selectAll('.arc')
          .data(pie(data))
          .enter()
          .append('g')
          .attr('class', 'arc');

        g.append('path')
          .attr('d', arc)
          .style('fill', function(d, i) { return color[i]; });

      });
    };

    new DashboardGraph();
  };

})();
