define(['jquery', 'twigjs', 'lib/components/base/modal'], function ($, Twig, Modal) {
  var CustomWidget = function () {
    let self = this;

    this.getTemplate = _.bind(function (template, params, callback) {
      params = (typeof params == 'object') ? params : {};
      template = template || '';

      return this.render({
        href: '/templates/' + template + '.twig',
        base_path: this.params.path,
        v: this.get_version(),
        load: callback
      }, params);
    }, this);

    self.getModal = function (data) {
        new Modal({
        class_name: 'modal-window',
        init: function ($modal_body) {
          $modal_body
            .trigger('modal:loaded')
            .html(data)
            .trigger('modal:centrify')
            .append('<span class="modal-body__close"><span class="icon icon-modal-close"></span></span>')
        },
        destroy: function () { }
      });
    }


    this.callbacks = {
      settings: function () {
      },
      dpSettings: function () {
      },
      init: function () {
        return true;
      },
      render: function () {
        if (AMOCRM.getWidgetsArea() == 'leads_card') {
          
          // console.log(AMOCRM.getWidgetsArea())
          self.render_template({
            render: self.render({ ref: '/tmpl/controls/button.twig' }, {
              id: 'allprod',
              name: 'buttonproducts',
              text: self.i18n('settings').button_title
            })
          }, { count: 10 });
        }
        return true;
      },
      bind_actions: function () {

        if (AMOCRM.getWidgetsArea() == 'leads_card') {
          $('#allprod').on('click', function (e) {
            $('#allprod').trigger('button:load:start');
            $.ajax({
              url: 'http://emfy.ct96865.tw1.ru/app/get_data.php?lead_id=' + AMOCRM.constant('card_id') + '&my_secret=aass',
              type: 'GET',
              // headers: {'ngrok-skip-browser-warning': 'some_value'},
              success: function (data) {
                // console.log('success');
                // console.log(data);
                self.getModal(data);
                $('#allprod').trigger('button:load:stop');
              },
              error: function (jqXHR, textStatus, errorThrown) {
                self.getModal('<div>Error status ' + textStatus + '-' + jqXHR.status + '</div>');
                $('#allprod').trigger('button:load:stop');
              }
            });

          });

        }

        return true;
      },
      onSave: function () {
        return true;
      }
    };
    return this;
  };
  return CustomWidget;
});