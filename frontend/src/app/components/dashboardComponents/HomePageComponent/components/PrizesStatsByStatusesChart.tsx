"use client";
import React, {useEffect, useState} from 'react';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Pie } from 'react-chartjs-2';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

ChartJS.register(ArcElement, Tooltip, Legend);



export default function PrizesStatsByStatusesChart(dataChart: any) {

    const[chartLabels, setChartLabels] = useState<string[]>([]);
    const[chartData, setChartData] = useState<number[]>([]);

    useEffect(() => {

        if(dataChart['dataChart']){
            if (dataChart['dataChart']) {
                let values = Object.values(dataChart['dataChart']);
                let chartData = values.map((value: any) => value['value']);

                setChartData(chartData as number[]);
            }


        }
    }, []);

    const data = {
        labels: ["Imprimé" , "En attente de validation", "Cadeau Remis", "Annulé", "Expiré"],
        datasets: [
            {
                label: 'Lots gagnés',
                data: chartData,
                backgroundColor: [
                    '#42B2FF',
                    '#E3E94B',
                    '#EBB3E6',
                    '#ff7b7b',
                    '#fa6084'
                ],
                borderColor: [
                    '#42B2FF',
                    '#E3E94B',
                    '#EBB3E6',
                    '#ff7b7b',
                    '#fa6084'
                ],
                borderWidth: 1,
            },
        ],
    };
    const inputRef = React.useRef<HTMLInputElement>(null);
    const options = {
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function (context: any) {
                        let label = context.label || '';
                        let value = context.parsed || 0;

                        if (isNaN(value)) {
                            return `${label}: N/A`;
                        }

                        let total = chartData.reduce((acc:any, data:any) => acc + data.value, 0);

                        if (isNaN(total) || total === 0) {
                            return `${label}: ${value} tickets`;
                        }

                        let percentage = ((value / total) * 100).toFixed(2) + '%';
                        return `${label}: ${value} (${percentage})`;
                    },
                },
            },
        },
    };



    return <Pie  id="myChart" key={"chart-div"} options={options as any} className={`${styles.chartCircle} ${styles.chartCircleAge} `} data={data} />;
}
