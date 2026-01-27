<?php
// $languageDirection = $_COOKIE['language'] == 'ar' ? 'rtl' : 'ltr';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Invoice</title>
  <link rel=icon href={{ asset('images/logo.svg') }}>

  <!-- CSS Files -->
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/invoice_pos.css')}}">
  <script src="{{asset('/assets/js/vue.js')}}"></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      font-size: 12px;
    }
    
    #invoice-POS {
      width: 80mm;
      margin: 0 auto;
      padding: 5px;
      background: #FFF;
      position: relative;
      overflow: hidden;
    }

    #invoice-POS::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('{{ asset('images/watermark.jpg') }}');
      background-position: center;
      background-repeat: no-repeat;
      background-size: 80%;
      opacity: 0.1;
      z-index: 0;
      pointer-events: none;
    }

    /* Make sure all content is above the watermark */
    #invoice-POS > * {
      position: relative;
      z-index: 1;
    }
    
    .logo-container {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
      border-bottom: 1px dotted #9cf;
      padding-bottom: 5px;
    }
    
    .logo-box {
      border: 1px solid #999;
      border-radius: 5px;
      padding: 2px 10px;
      font-style: italic;
      font-weight: bold;
      font-size: 14px;
    }
    
    .company-name {
      text-align: center;
      font-weight: bold;
      font-size: 16px;
      margin: 5px 0;
    }
    
    .company-phone {
      text-align: center;
      margin-bottom: 10px;
    }
    
    .invoice-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    
    .invoice-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .invoice-table th, .invoice-table td {
      border: 1px solid #999;
      padding: 5px;
    }
    
    .invoice-table th {
      background-color: #f9f9f9;
    }
    
    .qty-col {
      width: 15%;
    }
    
    .details-col {
      width: 50%;
    }
    
    .rate-col, .amount-col {
      width: 17.5%;
    }
    
    .summary-table {
      width: 100%;
      margin-top: 10px;
    }
    
    .summary-table td {
      padding: 3px 0;
    }
    
    .summary-label {
      text-align: right;
      padding-right: 10px;
    }
    
    .summary-value {
      border-bottom: 1px solid #999;
    }
    
    .signature-section {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
      margin-bottom: 10px;
    }
    
    .signature-box {
      width: 45%;
    }
    
    .signature-line {
      border-top: 1px solid #999;
      margin-top: 20px;
      padding-top: 5px;
    }
    
    .footer-note {
      border-top: 1px solid #999;
      margin-top: 10px;
      padding-top: 5px;
      text-align: center;
      font-size: 10px;
    }
    
    .hidden-print {
      margin-bottom: 20px;
    }
    
    @media print {
      .hidden-print {
        display: none;
      }
      
      #invoice-POS {
        width: 100%;
        margin: 0;
        padding: 0;
      }
      
      #invoice-POS::before {
        opacity: 0.1;
      }
    }
  </style>
</head>

<body>
  <div id="in_pos">
    <div class="hidden-print">
      <a @click="print_pos()" class="btn btn-primary">{{ __('translate.print') }}</a>
      <br>
    </div>
    
    <div id="invoice-POS">
      <!-- Logo Section -->
      {{-- <div class="logo-container">
        <div class="logo-box">Optical</div>
        <div class="logo-box"></div>
        <div class="logo-box">Solution</div>
      </div>
       --}}
      <!-- Company Info -->
      <div class="company-name">@{{setting.CompanyName}}</div>
      <div class="company-phone">Phone: @{{setting.CompanyPhone}}</div>
      
      <!-- Invoice Header -->
      <div class="invoice-header">
        <div>Bill # @{{sale.Ref}}</div>
        <div>Date: @{{sale.date}}</div>
      </div>
      
      <div class="customer-info">
        <div>M/s: @{{sale.client_name}}</div>
      </div>
      
      <!-- Invoice Table -->
      <table class="invoice-table">
        <thead>
          <tr>
            <th class="qty-col">Qty.</th>
            <th class="details-col">Details</th>
            <th class="rate-col">Rate</th>
            <th class="amount-col">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="detail_invoice in details">
            <td>@{{formatNumber(detail_invoice.quantity,2)}}</td>
            <td>
              @{{detail_invoice.name}}
              <span v-show="detail_invoice.is_imei && detail_invoice.imei_number !==null">
                <br>IMEI_SN: @{{detail_invoice.imei_number}}
              </span>
            </td>
            <td>@{{detail_invoice.price}}</td>
            <td>@{{detail_invoice.total}}</td>
          </tr>
          <!-- Empty rows to match the receipt design -->
          <tr v-for="n in (10 - details.length)" v-if="details.length < 10">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3" class="summary-label">Total</td>
            <td>@{{sale.GrandTotal}}</td>
          </tr>
        </tbody>
      </table>
      
      <!-- Summary Section -->
      <table class="summary-table">
        <tr>
          <td colspan="2" class="summary-label">Previous Bal.:</td>
          <td class="summary-value">@{{previous_balance}}</td>
        </tr>
        <tr>
          <td colspan="2" class="summary-label">Total Due:</td>
          <td class="summary-value">@{{remaining_balance}}</td>
        </tr>
        <tr>
          <td colspan="2" class="summary-label">Discount:</td>
          <td class="summary-value">@{{sale.discount}}</td>
        </tr>
        <tr>
          <td colspan="2" class="summary-label">Net. Due:</td>
          <td class="summary-value">@{{sale.GrandTotal}}</td>
        </tr>
        <tr>
          <td colspan="2" class="summary-label">Amount Received:</td>
          <td class="summary-value">@{{sale.paid_amount}}</td>
        </tr>
        <tr>
    <td colspan="2" class="summary-label">Discount:</td>
    <td class="summary-value">
        <div>{{ $sale['discount'] }}</div>
        @if($sale['product_discount_total'] > 0)
            <div>+ {{ $sale['product_discount_total'] }} <small class="text-muted">(Product Discounts)</small></div>
        @endif
    </td>
</tr>
      </table>
      
      <!-- Signature Section -->
      <div class="signature-section">
        <div class="signature-box">
          <div class="signature-line">Sign.</div>
        </div>
        <div class="signature-box">
          <div class="signature-line">Customer Sign.</div>
        </div>
      </div>
      
      <!-- Footer Note -->
      <div class="footer-note" v-show="pos_settings.show_note">
        <p>{{ __('translate.Thank_You_For_Shopping_With_Us_Please_Come_Again') }}</p>
      </div>
    </div>
  </div>

  <script src="{{asset('/assets/js/jquery.min.js')}}"></script>

  <script>
    var app = new Vue({
      el: '#in_pos',
      
      data: {
        payments: @json($payments),
        details: @json($details),
        pos_settings: @json($pos_settings),
        sale: @json($sale),
        setting: @json($setting),
        previous_balance: @json($previous_balance ?? 0),
      },
      
      mounted() {
        if (this.pos_settings.is_printable) {
          this.print_pos();
        }
      },
      
      methods: {
        isPaid() {
          return parseFloat(this.sale.paid_amount.replace(/[^\d.-]/g, '')) > 0;
        },
        
        isPaidLessThanTotal() {
          return parseFloat(this.sale.paid_amount.replace(/[^\d.-]/g, '')) < parseFloat(this.sale.GrandTotal.replace(/[^\d.-]/g, ''));
        },
        
        formatNumber(number, dec) {
          const value = (typeof number === "string" ? number : number.toString()).split(".");
          if (dec <= 0) return value[0];
          let formated = value[1] || "";
          if (formated.length > dec)
            return `${value[0]}.${formated.substr(0, dec)}`;
          while (formated.length < dec) formated += "0";
          return `${value[0]}.${formated}`;
        },
        
        print_pos() {
          // Get all styles from the current page
          var styles = '';
          for (var i = 0; i < document.styleSheets.length; i++) {
            var sheet = document.styleSheets[i];
            try {
              var rules = sheet.cssRules || sheet.rules;
              if (rules) {
                for (var j = 0; j < rules.length; j++) {
                  styles += rules[j].cssText + '\n';
                }
              }
            } catch (e) {
              // Skip cross-domain stylesheets
              console.log("Could not access stylesheet", e);
            }
          }
          
          // Get the invoice content
          var divContents = document.getElementById("invoice-POS").outerHTML;
          
          // Create a new window with all styles and content
          var printWindow = window.open('', '', 'height=600,width=800');
          printWindow.document.write('<html><head><title>Print Invoice</title>');
          printWindow.document.write('<style type="text/css">');
          printWindow.document.write(styles);
          printWindow.document.write(`
            @media print {
              body {
                margin: 0;
                padding: 0;
              }
              #invoice-POS {
                width: 80mm;
                margin: 0 auto;
                padding: 5px;
                background: #FFF;
                position: relative;
                overflow: hidden;
              }
              #invoice-POS::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url('{{ asset('images/watermark.jpg') }}');
                background-position: center;
                background-repeat: no-repeat;
                background-size: 80%;
                opacity: 0.1;
                z-index: 0;
                pointer-events: none;
              }
              #invoice-POS > * {
                position: relative;
                z-index: 1;
              }
              #invoice-table{
                background-color: transparent;
              }
               .summary-table td {
                background-color: transparent;
               }
                   #invoice-POS {
      width: 80mm;
      margin: 0 auto;
      padding: 5px;
      background: #FFF;
      position: relative;
      overflow: hidden;
    }

    #invoice-POS::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('{{ asset('images/watermark.jpg') }}');
      background-position: center;
      background-repeat: no-repeat;
      background-size: 80%;
      opacity: 0.1;
      z-index: 0;
      pointer-events: none;
    }

            }
          `);
          printWindow.document.write('</style></head><body>');
          printWindow.document.write(divContents);
          printWindow.document.write('</body></html>');
          printWindow.document.close();
          
          // Wait for everything to load before printing
          setTimeout(function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
          }, 1000);
        }
      },
      
      computed: {
        remaining_balance() {
          const previousBalanceValue = parseFloat(this.previous_balance.toString().replace(/[^\d.-]/g, '')) || 0;
          const currentDue = parseFloat(this.sale.due.toString().replace(/[^\d.-]/g, '')) || 0;
          const currencySymbol = this.previous_balance.toString().replace(/[\d., ]/g, '');
          const totalRemaining = previousBalanceValue + currentDue;
          return currencySymbol + this.formatNumber(totalRemaining, 2);
        }
      },
    });
  </script>
</body>
</html>