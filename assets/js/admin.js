(function() {
  'use strict';

  window.onload = function() {
    var Years = function() {
      this.select = document.getElementById('js-wpkpid-years-select');

      this.setEvents();
      this.changeTable(this.select);
    };

    Years.prototype.setEvents = function() {
      var self = this;
      this.select.addEventListener('change', function(e) {
        self.changeTable(e.target);
      });
    };

    Years.prototype.changeTable = function(select) {
      var year = select.options[select.selectedIndex].value;
      var tables = document.querySelectorAll('.js-wpkpid-years-table');

      _.forEach(tables, function(table) {
        if(table.id === 'js-wpkpid-years-table-' + year) {
          table.style.display = 'block';
        } else {
          table.style.display = 'none';
        }
      });
    };

    new Years();
  };

})();