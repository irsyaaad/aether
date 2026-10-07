@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const drTglInput = document.getElementById('dr_tgl');
            const spTglInput = document.getElementById('sp_tgl');

            function fetchCalculationData() {
                const drTgl = drTglInput.value;
                const spTgl = spTglInput.value;

                // Bangun URL dengan parameter tanggal
                const url = new URL('{{ url('dashboard-pad/getCalculation') }}');
                if (drTgl) url.searchParams.append('dr_tgl', drTgl);
                if (spTgl) url.searchParams.append('sp_tgl', spTgl);

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Network response was not ok");
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data.data);
                        const totalIpt = Number(data.data.ipt || 0);
                        const totalIpr = Number(data.data.ipr || 0);
                        const totalGedung = Number(data.data.gedung || 0);
                        const totalLain = Number(data.data.lain || 0);
                        const totalSemua = Number(totalIpt + totalIpr + totalGedung + totalLain);

                        animateCounter(document.getElementById('total_ipt'), totalIpt);
                        animateCounter(document.getElementById('total_ipr'), totalIpr);
                        animateCounter(document.getElementById('total_gedung'), totalGedung);
                        animateCounter(document.getElementById('total_lain'), totalLain);
                        animateCounter(document.getElementById('total_semua'), totalSemua);

                        setChart(document.getElementById('iptChart').getContext('2d'), data.data.chartIpt ||
                    []);
                        donutChart(document.getElementById('donutChart').getContext('2d'), totalIpt,
                            totalSemua);
                        barChat(document.getElementById('bulanChart').getContext('2d'), data.data
                            .chartIptBulan || []);
                    })
                    .catch(error => {
                        console.error('Error fetching calculation data:', error);
                    });
            }

            // Fungsi animasi counter
            function animateCounter(element, target) {
                if (!element) return;

                target = Number(target);
                // Jika bukan angka valid atau 0, tampilkan "0"
                if (!target || isNaN(target) || target <= 0) {
                    element.textContent = "0";
                    return;
                }

                let current = 0;
                const increment = Math.ceil(target / 50);

                const interval = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(interval);
                    }
                    element.textContent = current.toLocaleString('id-ID');
                }, 20);
            }

            let iptChart = null;
            let comparationChart = null;
            let bulanChart = null;

            function setChart(ctx, mutasiData) {

                // Urutkan data berdasarkan hari (opsional)
                mutasiData.sort((a, b) => a.hari - b.hari);

                // Siapkan data untuk chart
                const labels = mutasiData.map(item => `${item.hari}`);
                const totals = mutasiData.map(item => Number(item.total));

                if (iptChart) {
                    iptChart.destroy();
                }

                iptChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Masuk IPT',
                            data: totals,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Jumlah (Rp)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function donutChart(ctx, ipt, total) {
                const iptValue = Number(ipt);
                const totalValue = Number(total);
                const otherValue = Math.max(totalValue - iptValue, 0);

                const labels = ['IPT', 'Pemasukan Lain-lain'];
                const values = [iptValue, otherValue];

                if (comparationChart) {
                    comparationChart.destroy();
                }

                comparationChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: [
                                '#36A2EB', // IPT
                                '#E0E0E0' // Sisa
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false, // pastikan bisa menyesuaikan kontainer
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        const percent = ((value / totalValue) * 100).toFixed(2);
                                        return `${context.label}: ${percent}%`;
                                    }
                                }
                            },
                            legend: {
                                position: 'right',
                                labels: {
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const value = data.datasets[0].data[i];
                                                return {
                                                    text: `${label}: Rp ${value.toLocaleString('id-ID')}`,
                                                    fillStyle: data.datasets[0].backgroundColor[
                                                        i],
                                                    strokeStyle: data.datasets[0]
                                                        .backgroundColor[i],
                                                    index: i
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'Persentase IPT terhadap Total Mutasi'
                            }
                        }
                    }
                });
            }

            function barChat(ctx, serverData) {
                // Inisialisasi data 12 bulan dengan 0
                const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const values = Array(12).fill(0);

                // Masukkan nilai ke posisi bulan yg sesuai (bulan 1 = index 0)
                serverData.forEach(item => {
                    const index = Number(item.bulan) - 1;
                    values[index] = Number(item.total);
                });

                if (bulanChart) {
                    bulanChart.destroy();
                }

                bulanChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Pembayaran Retribusi (IPT) per Bulan',
                            data: values,
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Jumlah (Rp)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Bulan'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            },
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Total Pembayaran Retribusi (IPT) per Bulan'
                            }
                        }
                    }
                });
            }

            // Panggil saat DOM pertama kali selesai
            fetchCalculationData();

            // Tambahkan event listener jika ingin memuat ulang data saat tanggal diubah
            drTglInput.addEventListener('change', fetchCalculationData);
            spTglInput.addEventListener('change', fetchCalculationData);

            // Sinkronisasi mutasi setiap hari
            const APP_ENV = "{{ app()->environment() }}"; // Laravel akan isi dari .env (local, production, etc.)

            function getTodayDate() {
                const today = new Date();
                return today.toISOString().split('T')[0]; // format YYYY-MM-DD
            }

            function syncMutasi(tgl) {
                const url = new URL('{{ url('/sync-mutasi') }}');
                url.searchParams.append('tgl', tgl);

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Gagal sinkronisasi mutasi");
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Sinkronisasi berhasil:", data);
                        fetchCalculationData(); // Panggil ulang data dashboard
                    })
                    .catch(error => {
                        console.error("Error sync mutasi:", error);
                    });
            }

            // Jalankan sync hanya jika environment bukan 'local' atau 'development'
            if (APP_ENV !== 'local' && APP_ENV !== 'development') {
                // Jalankan sekali saat load
                syncMutasi(getTodayDate());

                // Jalankan berkala setiap 2 menit (120000 ms)
                setInterval(() => {
                    syncMutasi(getTodayDate());
                }, 300000);
            } else {
                console.log("Sync mutasi dimatikan di environment development.");
            }
        });
    </script>
@endsection
