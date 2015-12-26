(function() {
  'use strict';

  window.onload = function() {
    var GA = function() {
      this.$select = document.getElementById('js-wpkpidb-account-select');
      this.$properties = document.getElementById('js-wpkpidb-properties');

      this.propertyUrl = '';

      this.setEvents();
    };

    GA.prototype.setEvents = function() {
      var self = this;
      this.$select.addEventListener('change', function(e) {
        self.properties(e.target);
      });
    };

    GA.prototype.properties = function(target) {
      var select = document.createElement('select');
      var account = target.value;
      var response = this._getProperties(account);

      select.name = 'ga_property';

    };

    GA.prototype._getProperties = function(account) {
      var xmlHttpRequest = new XMLHttpRequest();

      xmlHttpRequest.addEventListener('loadend', function() {
        if(xmlHttpRequest.status === 200) {
          var response = xmlHttpRequest.response;
          return response;
        }
      });
      xmlHttpRequest.open('POST', this.propertyUrl, true);
      xmlHttpRequest.send('ajax_ga_account=' + account);
    };

    new GA();


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
  };

})();