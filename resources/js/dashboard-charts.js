/**
 * Dashboard Charts JavaScript
 * Handles all chart rendering for the dashboard
 */

(function() {
    'use strict';

    // Global chart instances
    let leadsChart = null;
    let stagesChart = null;

    /**
     * Initialize all charts when DOM is ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        initLeadsChart();
        initStagesChart();
    });

    /**
     * Initialize Leads Over Time Chart
     */
    function initLeadsChart() {
        const leadsCtx = document.getElementById('leadsChart');
        if (!leadsCtx) return;

        // Get data from data attribute
        const leadsDataElement = document.getElementById('leadsChart');
        let leadsData = [];
        
        if (leadsDataElement && leadsDataElement.dataset.leads) {
            try {
                leadsData = JSON.parse(leadsDataElement.dataset.leads);
            } catch (e) {
                console.error('Error parsing leads data:', e);
                return;
            }
        } else {
            console.warn('Leads data not found in data attribute');
            return;
        }

        // Extract labels and data
        const labels = leadsData.map(item => item.date);
        const data = leadsData.map(item => item.count);

        // Calculate max value for better Y-axis scaling
        const maxValue = Math.max(...data, 1);

        leadsChart = new Chart(leadsCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'New Leads',
                    data: data,
                    borderColor: '#84c373',
                    backgroundColor: 'rgba(132, 195, 115, 0.15)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#84c373',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#6ba85a',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            color: '#495057'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 31, 46, 0.95)',
                        padding: 12,
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                return 'Date: ' + context[0].label;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                return 'New Leads: ' + count + (count === 1 ? ' lead' : ' leads');
                            },
                            afterLabel: function(context) {
                                const index = context.dataIndex;
                                if (leadsData[index] && leadsData[index].day) {
                                    return 'Day: ' + leadsData[index].day;
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.ceil(maxValue * 1.2),
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            color: '#6c757d',
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        },
                        grid: {
                            color: 'rgba(132, 195, 115, 0.1)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            color: '#6c757d'
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    /**
     * Initialize Lead Stages Doughnut Chart
     */
    function initStagesChart() {
        const stagesCtx = document.getElementById('stagesChart');
        if (!stagesCtx) return;

        // Get data from data attribute
        let stagesData = {};
        
        const stagesDataElement = document.getElementById('stagesChart');
        if (stagesDataElement && stagesDataElement.dataset.stages) {
            try {
                stagesData = JSON.parse(stagesDataElement.dataset.stages);
            } catch (e) {
                console.error('Error parsing stages data:', e);
                return;
            }
        } else {
            console.warn('Stages data not found in data attribute');
            return;
        }

        const stageColors = {
            'new_lead': '#17a2b8',
            'in_progress': '#6c757d',
            'qualified': '#84c373',
            'not_qualified': '#6c757d',
            'junk': '#6c757d'
        };

        const stageLabels = Object.keys(stagesData);
        const stageValues = Object.values(stagesData);
        const stageColorsArray = stageLabels.map(stage => stageColors[stage] || '#6c757d');

        stagesChart = new Chart(stagesCtx, {
            type: 'doughnut',
            data: {
                labels: stageLabels.map(label => 
                    label.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
                ),
                datasets: [{
                    data: stageValues,
                    backgroundColor: stageColorsArray,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 11
                            },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Update charts on window resize
     */
    window.addEventListener('resize', function() {
        if (leadsChart) {
            leadsChart.resize();
        }
        if (stagesChart) {
            stagesChart.resize();
        }
    });

    // Export chart instances for external access
    window.dashboardCharts = {
        leadsChart: function() { return leadsChart; },
        stagesChart: function() { return stagesChart; }
    };

})();

