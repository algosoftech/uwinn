
<style>
  .chart-legend {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 10px 20px;
    max-width: 100%;
    justify-content: center;
    margin-top: 15px;
    font-size: 14px;
  }

  .chart-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
  }

  .chart-legend-color {
    width: 20px;
    height: 12px;
    border-radius: 2px;
  }
</style>

<!-- Loding animation CSS -->

<div class="pcoded-main-container">
   <div class="pcoded-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
         <div class="page-block">
            <div class="row align-items-center">
               <div class="col-md-12">
                  <div class="page-header-title">
                     <?php /* ?>
                     <h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5>
                     <?php */ ?>
                  </div>
                  <ul class="breadcrumb">
                     <li class="breadcrumb-item">
                        <a href="<?=base_url('/maindashboard');?>">
                           <i class="feather icon-home"></i>
                        </a>
                     </li>
                     <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <!-- [ breadcrumb ] end -->
      <!-- [ Main Content ] start -->
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-header">
                  <h5>Dashboard</h5>
                   <form id="Data_Form" method="get" action="<?= $forAction ?>">
                  <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
                     <select id="rangeType" name="rangeType" class="custom-select custom-select-sm w-auto mr-2">
                        <option value="today" <?=$type == 'today' ? 'selected' : ''?>>Today</option>
                        <option value="yesterday" <?=$type == 'yesterday' ? 'selected' : ''?>>Yesterday</option>
                        <option value="last7" <?=$type == 'last7' ? 'selected' : ''?>>Last 7 Days</option>
                        <option value="last30" <?=$type == 'last30' ? 'selected' : ''?>>Last 30 Days</option>
                        <option value="thisMonth" <?=$type == 'thisMonth' ? 'selected' : ''?>>This Month</option>
                        <option value="lastMonth" <?=$type == 'lastMonth' ? 'selected' : ''?>>Last Month</option>
                        <option value="custom" <?=$type == 'custom' ? 'selected' : ''?>>Custom Range</option>
                     </select>
                     <input id="dateRange" name="dateRange" class="form-control form-control-sm w-auto <?=$type != 'custom' ? 'd-none':''?>" placeholder="Select date range" value="<?= ($type == 'custom' && !empty($dateRange)) ? $dateRange : '' ?>">
                  </div>
                  </form>
                  
               </div>
               <div class="card-body">
                  <div class="card-block">
                     <div class="row ">
                        <div class="container-fluid">
                           <div class="panel panel-headline">
                              <div class="panel-body">
                                 <div class="row box_guard">
                                    
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div style="display: flex; justify-content: space-between; align-items: start;">
                                          <h3>Total Ticket Sales</h3>
                                          <div style="color: #dc2626; font-size: 14px; font-weight: 600;">
                                                <span style="transform: rotate(225deg); display: inline-block;">↘</span> -1%
                                          </div>
                                       </div>

                                       <div class="metrics">
                                          <div><strong>B2B:</strong> <b><?=formatNumberShort($ticket_total_btb)?></b> Tickets AED <b><?=formatNumberShort($ticket_total_btb_amount)?></b></div>
                                          <div><strong>B2C:</strong> <b><?=formatNumberShort($ticket_total_btc)?></b> Tickets AED <b><?=formatNumberShort($ticket_total_btc_amount)?></b></div>
                                       </div>
                                      <canvas id="ticketChart"  height="200"></canvas>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div style="display: flex; justify-content: space-between; align-items: start;">
                                          <h3>Prize Redeemed</h3>
                                          <div style="color: #dc2626; font-size: 14px; font-weight: 600;">
                                                <span style="transform: rotate(225deg); display: inline-block;">↘</span> -1%
                                          </div>
                                       </div>

                                       <div class="metrics">
                                          <div><strong>B2B:</strong> <b><?=formatNumberShort($winner_total_btb_voucher)?></b> Vouchers AED <b><?=formatNumberShort($winner_total_btb_amount)?></b></div>
                                          <div><strong>B2C:</strong> <b><?=formatNumberShort($winner_total_btc_voucher)?></b> Vouchers AED <b><?=formatNumberShort($winner_total_btc_amount)?></b></div>
                                       </div>
                                       <canvas id="winnerChart"  height="200"></canvas>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                          <div style="display: flex; justify-content: space-between; align-items: start;">
                                          <h3>Online Purchase </h3>
                                          <div style="color: #dc2626; font-size: 14px; font-weight: 600;">
                                                <span style="transform: rotate(225deg); display: inline-block;">↘</span> -1%
                                          </div>
                                       </div>

                                       <div class="metrics">
                                          <div><strong>Stripe:</strong> <b><?=formatNumberShort($total_stripe)?></b> Purchases AED <b><?=formatNumberShort($total_stripe_amount)?></b></div>
                                          <div><strong>CCAvenue:</strong> <b><?=formatNumberShort($total_ccavenue)?></b> Purchases AED <b><?=formatNumberShort($total_ccavenue_amount)?></b></div>
                                       </div>
                                       <canvas id="OnlinePurchaseChart"  height="200"></canvas>
                                    </div>
                                    <!-- Referral Report -->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div style="display: flex; justify-content: space-between; align-items: start;">
                                          <h3>B2B to B2C Upoint </h3>
                                          <div style="color: #dc2626; font-size: 14px; font-weight: 600;">
                                                <span style="transform: rotate(225deg); display: inline-block;">↘</span> -1%
                                          </div>
                                       </div>

                                       <div class="metrics">
                                          <div><strong>Transfer:</strong> <b><?=formatNumberShort($total_movedwinningprize)?></b> Transfers AED <b><?=formatNumberShort($total_movedwinningprize_amount)?></b></div>
                                          <div><strong>Voucher:</strong> <b><?=formatNumberShort($total_rechargecoupon)?></b> Transfers AED <b><?=formatNumberShort($total_rechargecoupon_amount)?></b></div>
                                       </div>
                                       <canvas id="b2btob2cupointChart"  height="200"></canvas>
                                    </div>
                                    <!-- END -->
                                    <!-- Signup Bonus Report -->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div style="display: flex; justify-content: space-between; align-items: start;">
                                          <h3>Signups </h3>
                                          <div style="color: #dc2626; font-size: 14px; font-weight: 600;">
                                                <span style="transform: rotate(225deg); display: inline-block;">↘</span> -1%
                                          </div>
                                       </div>

                                       <div class="metrics">
                                          <div><strong>B2B:</strong> <b><?=formatNumberShort($total_btb_user)?></b> Users</div>
                                          <div><strong>B2C:</strong> <b><?=formatNumberShort($total_btc_user)?></b> Users</div>
                                          
                                       </div>
                                       <canvas id="signupChart"  height="200"></canvas>
                                    </div>
                                    <!-- END -->

                                     <!-- Membership Cashback Report -->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div style="display: flex; justify-content: space-between; align-items: start;">
                                          <h3>Referral </h3>
                                          <div style="color: #dc2626; font-size: 14px; font-weight: 600;">
                                                <span style="transform: rotate(225deg); display: inline-block;">↘</span> -1%
                                          </div>
                                       </div>

                                       <div class="metrics">
                                          <div><strong>B2B:</strong> <b><?=formatNumberShort($total_btb_refuser)?></b> Referrals</div>
                                          <div><strong>B2C:</strong> <b><?=formatNumberShort($total_btc_refuser)?></b> Referrals</div>
                                          
                                       </div>
                                       <canvas id="refsignupChart"  height="200"></canvas>
                                    </div>
                                    <!-- END -->

                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                      <canvas id="weeklywinnerChart" height="200"></canvas>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>


  function renderLineChart({ ctxId, labels, b2bData, b2cData, valueLabel = 'Tickets',cardl1,cardl2 }) {
    const isHourly = labels[0]?.includes('h');

    new Chart(document.getElementById(ctxId).getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: cardl1,
            data: b2bData,
            borderColor: '#1d4ed8',
            backgroundColor: 'rgba(29, 78, 216, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 0
          },
          {
            label: cardl2,
            data: b2cData,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 0
          }
        ]
      },
      options: {
        responsive: true,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            display: true,
            position: 'bottom'
          },
          tooltip: {
            enabled: true,
            backgroundColor: '#fff',
            borderColor: '#ccc',
            borderWidth: 1,
            titleColor: '#111',
            bodyColor: '#111',
            titleFont: { weight: 'bold' },
            callbacks: {
              title: function (context) {
                return `${isHourly ? 'Hour' : 'Date'}: ${context[0].label}`;
              },
              label: function (context) {
                return `${context.dataset.label} ${valueLabel}: ${context.raw}`;
              }
            }
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: isHourly ? 'Hour' : 'Date',
              color: '#333',
              font: {
                weight: 'bold'
              }
            }
          },
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: valueLabel,
              color: '#333',
              font: {
                weight: 'bold'
              }
            }
          }
        }
      }
    });
  }


  renderLineChart({
    ctxId: 'ticketChart',
    labels: <?= json_encode($ticket_hours); ?>,
    b2bData: <?= json_encode($ticket_btb_data); ?>,
    b2cData: <?= json_encode($ticket_btc_data); ?>,
    valueLabel: 'Tickets',
    cardl1:'B2B',
    cardl2:'B2C',
  });

  
  renderLineChart({
    ctxId: 'winnerChart',
    labels: <?= json_encode($winner_hours); ?>,
    b2bData: <?= json_encode($winner_btb_data); ?>,
    b2cData: <?= json_encode($winner_btc_data); ?>,
    valueLabel: 'Vouchers',
    cardl1:'B2B',
    cardl2:'B2C',
  });


 renderLineChart({
  ctxId: 'OnlinePurchaseChart',
  labels: <?= json_encode($payment_hour); ?>,
  b2bData: <?= json_encode($ccavenue); ?>,
  b2cData: <?= json_encode($stripe); ?>,
  valueLabel: 'Purchase',
  cardl1:'CCAVENUE',
  cardl2:'STRIPE',
});
renderLineChart({
  ctxId: 'b2btob2cupointChart',
  labels: <?= json_encode($transfer_hour); ?>,
  b2bData: <?= json_encode($movedwinningprize); ?>,
  b2cData: <?= json_encode($rechargecoupon); ?>,
  valueLabel: 'B2B to B2C',
  cardl1:'Transfer Transfers',
  cardl2:'Voucher Transfers',
});
renderLineChart({
  ctxId: 'signupChart',
  labels: <?= json_encode($user_hour); ?>,
  b2bData: <?= json_encode($btb_user); ?>,
  b2cData: <?= json_encode($btc_user); ?>,
  valueLabel: 'Users',
  cardl1:'B2B',
  cardl2:'B2C',
});

renderLineChart({
  ctxId: 'refsignupChart',
  labels: <?= json_encode($refuser_hour); ?>,
  b2bData: <?= json_encode($btb_refuser); ?>,
  b2cData: <?= json_encode($btc_refuser); ?>,
  valueLabel: 'Referral',
  cardl1:'B2B',
  cardl2:'B2C',
});
 </script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
const form = document.getElementById("Data_Form");
const dateInput = document.getElementById("dateRange");
const rangeType = document.getElementById("rangeType");

const fp = flatpickr(dateInput, {
  mode: "range",
  dateFormat: "Y-m-d",
  onClose: function(selectedDates) {
    if (rangeType.value === "custom") {
      if (selectedDates.length === 2) {
        const diff = Math.abs(selectedDates[1] - selectedDates[0]) / (1000 * 60 * 60 * 24);
        if (diff > 30) {
          alert("Please select a maximum range of 30 days.");
          fp.clear();
        } else {
          form.submit();
        }
      }
    }
  }
});

rangeType.addEventListener("change", function () {
  const type = this.value;
  if (type === "custom") {
    dateInput.classList.remove("d-none");
    fp.clear();
  } else {
    dateInput.classList.add("d-none");
    setDateRange(type);
    form.submit();
  }
});

function setDateRange(type) {
  const today = new Date();
  let from = new Date(), to = new Date();

  switch(type) {
    case "yesterday":
      from.setDate(today.getDate() - 1);
      to = new Date(from);
      break;
    case "last7":
      from.setDate(today.getDate() - 6);
      break;
    case "last30":
      from.setDate(today.getDate() - 29);
      break;
    case "thisMonth":
      from = new Date(today.getFullYear(), today.getMonth(), 1);
      to = new Date(today.getFullYear(), today.getMonth() + 1, 0);
      break;
    case "lastMonth":
      from = new Date(today.getFullYear(), today.getMonth() - 1, 1);
      to = new Date(today.getFullYear(), today.getMonth(), 0);
      break;
    default:
      return;
  }

  fp.setDate([from, to]);
}
</script>

<script>
const labels = <?= json_encode($hours); ?>;
const b2bData = <?= json_encode($btb_data); ?>;
const b2cData = <?= json_encode($btc_data); ?>;
const isHourly = labels[0]?.includes('h');

new Chart(document.getElementById('ticketChart').getContext('2d'), {
  type: 'line',
  data: {
    labels: labels,
    datasets: [
      {
        label: 'B2B',
        data: b2bData,
        borderColor: '#1d4ed8',
        backgroundColor: 'rgba(29, 78, 216, 0.1)',
        tension: 0.4,
        fill: true,
        pointRadius: 0
      },
      {
        label: 'B2C',
        data: b2cData,
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true,
        pointRadius: 0
      }
    ]
  },
  options: {
    responsive: true,
    interaction: {
      mode: 'index',
      intersect: false
    },
    plugins: {
      legend: {
        display: true,
        position: 'bottom'
      },
      tooltip: {
        enabled: true,
        backgroundColor: '#fff',
        borderColor: '#ccc',
        borderWidth: 1,
        titleColor: '#111',
        bodyColor: '#111',
        titleFont: { weight: 'bold' },
        callbacks: {
          title: function(context) {
            const label = context[0].label;
            return isHourly ? `Hour: ${label}` : `Date: ${label}`;
          },
          label: function(context) {
            return `${context.dataset.label} Tickets: ${context.raw}`;
          }
        }
      }
    },
    scales: {
      x: {
        title: {
          display: true,
          text: isHourly ? 'Hour' : 'Date'
        }
      },
      y: {
        beginAtZero: true,
        title: {
          display: true,
          text: 'Ticket Count'
        }
      }
    }
  }
});
</script>


<script>

   const rawData = <?php echo json_encode($chartData); ?>;
const rawArray = [];
  const allDates = Object.keys(rawData).sort();
  const allProductsSet = new Set();

  allDates.forEach(date => {
    const dayProducts = rawData[date];
    for (const product in dayProducts) {
      allProductsSet.add(product);
      rawArray.push({
        date,
        product,
        winner_count: dayProducts[product].winner_count,
        total_amount: dayProducts[product].total_amount
      });
    }
  });

  const allProducts = Array.from(allProductsSet).sort();

  const colorPalette = [
    '#FF9800', '#4CAF50', '#2196F3', '#E91E63', '#F44336',
    '#9C27B0', '#00BCD4', '#8BC34A', '#FF5722', '#03A9F4',
    '#CDDC39', '#607D8B', '#795548', '#673AB7', '#3F51B5',
    '#009688', '#FFC107', '#FFEB3B', '#9E9E9E', '#B71C1C'
  ];

  const datasets = allProducts.map((product, index) => {
    return {
      label: product,
      backgroundColor: colorPalette[index % colorPalette.length],
      data: allDates.map(date => {
        const item = rawData[date]?.[product];
        return item ? item.winner_count : 0;
      })
    };
  });

  const totalsByProduct = {};
  rawArray.forEach(entry => {
    const p = entry.product;
    if (!totalsByProduct[p]) {
      totalsByProduct[p] = { winner_count: 0, total_amount: 0 };
    }
    totalsByProduct[p].winner_count += entry.winner_count;
    totalsByProduct[p].total_amount += entry.total_amount;
  });

  const ctxn = document.getElementById('weeklywinnerChart').getContext('2d');
  new Chart(ctxn, {
    type: 'bar',
    data: {
      labels: allDates,
      datasets: datasets
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Weekly Game Winners'
        },
        tooltip: {
          callbacks: {
            label: function(ctxn) {
              const product = ctxn.dataset.label;
              const date = ctxn.label;
              const entry = rawData[date]?.[product];
              const win = entry?.winner_count || 0;
              const amt = entry?.total_amount || 0;
              return `${product}: ${win} winners, AED ${amt}`;
            }
          }
        },
        legend: {
          position: 'top',
          labels: {
            generateLabels: function(chart) {
              return chart.data.datasets.map((dataset, i) => {
                const product = dataset.label;
                const total = totalsByProduct[product] || { winner_count: 0, total_amount: 0 };
                return {
                  text: `${product} - ${total.winner_count} winners, AED ${total.total_amount}`,
                  fillStyle: dataset.backgroundColor,
                  strokeStyle: dataset.borderColor,
                  lineWidth: 1,
                  hidden: !chart.isDatasetVisible(i),
                  index: i
                };
              });
            }
          }
        }
      },
      interaction: {
        mode: 'index',
        intersect: false
      },
      scales: {
        x: {
          stacked: false
        },
        y: {
          stacked: false,
          beginAtZero: true,
          title: {
            display: true,
            text: 'Winner Count'
          }
        }
      }
    }
  });
</script>





