import React, { useEffect, useState } from 'react';
import { Bar } from 'react-chartjs-2';
import styles from '@/styles/pages/dashboards/storeAdminDashboard.module.css';

export default function PrizeStatsByGenderByAgeChart({ dataChart }: { dataChart: any }) {
    const [chartLabels, setChartLabels] = useState<string[]>([]);
    const [chartData, setChartData] = useState<number[][]>([]);

    useEffect(() => {
        if (dataChart) {
            let labels: string[] = [];
            let data: number[][] = [];

            dataChart.forEach((entry: any, index: number) => {
                const label = entry.label;
                const value = entry.value;

                labels.push(label);

                const keys = ['homme', 'femme'];

                keys.forEach((key, i) => {
                    if (!data[i]) {
                        data[i] = [];
                    }

                    data[i].push(value[key]);
                });
            });

            console.log(labels);
            console.log(data);
            setChartData(data);
            const labelsArray = ['Homme', 'Femme'];

            setChartLabels(labelsArray);
        }
    }, [dataChart]);

    const options = {
        plugins: {
            title: {
                display: true,
                text: 'Répartition des lots par genre et par âge',
            },
        },
        responsive: true,
        scales: {
            x: {
                stacked: false,
            },
            y: {
                stacked: false,
            },
        },
    };

    const colors = ['#42B2FF', 'rgba(253,109,234,0.76)'];

    const datasets = colors.map((color, index) => ({
        label: chartLabels[index],
        data: chartData[index],
        backgroundColor: color,
        borderColor: color,
        borderWidth: 1,
        barPercentage: 0.8,
    }));

    const data = {
        labels: ["18-25 ans", "26-35 ans", "36-45 ans", "46-55 ans", "56-65 ans"],
        datasets,
    };

    return (
        <div className={styles.barChartDiv}>
            <Bar className={styles.barChartElement} options={options} data={data} />
        </div>
    );
}
