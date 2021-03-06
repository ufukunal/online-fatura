 <style>
  .custom-combobox {
    position: relative;
    display: inline-block;
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
    height:20px;
    /* support: IE7 */
    *height: 1.7em;
    *top: 0.1em;
  }
  .custom-combobox-input {
    margin: 0;
    padding: 0.1em;
  }
  </style>
  <script>
  (function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Tüm Bilgileri Göster" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text(),
              val = $( this ).val();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              val : val, // value elde etmek için değer
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
        
         /* Kendi kodlarımm  */
         // secilen item value
       	//alert(ui.item.val);
          ajax_urunler(ui.item.val);
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " eşleşen veri yok.." )
          .tooltip( "open" );
        
        /* Kendi kodlarımm  */
        // hiç olmama durumu
        ajax_urunler(0);
         
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.data( "ui-autocomplete" ).term = "";
        
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
        
      }
    });
  })( jQuery );

  $(function() {
    $( "#fatura_no" ).combobox();    
  });
  // verilerin cekilip listelenmesi
  function ajax_urunler(fatura_pk) {
     $.ajax({
            url: "ajax-urun-ekle.php?fatura_pk=" + fatura_pk ,
            success: function(ajaxcevap){ $('#urun_ekleme').html(ajaxcevap).slideDown('slow'); },
            error : function() { alert('Hata oluştu..'); } 
        });
   } 
  </script>
 
       
<?php
     error_reporting(0);
	 session_start();
     require_once '../my_class/fatura_detay.php';

	 $sirket_pk = $_SESSION['sirket_pk'];

     echo ' <select id="fatura_no" name="fatura_no"  >
                 <option value="0" > Fatura No Seçiniz.. </option>';
									 
     $db = new fatura_detay();
     $faturalar = $db->listele($sirket_pk);
     if ($faturalar) {
        foreach ($faturalar as  $row) {
          echo "<option value='{$row['pk']}' > {$row['fatura_no']} </option> ";    
       }
     }
    echo "</select>";
?>   
 