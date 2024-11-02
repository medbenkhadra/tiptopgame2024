"use client";
import React, {useEffect, useState} from 'react';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Pie } from 'react-chartjs-2';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

ChartJS.register(ArcElement, Tooltip, Legend);



export default function PrizesStatsWithAgeChart(dataChart: any) {

    const[chartLabels, setChartLabels] = useState<string[]>([]);
    const[chartData, setChartData] = useState<number[]>([]);

    useEffect(() => {

        if(dataChart['dataChart']){
            let values = Object.values(dataChart['dataChart']);
            let obj: any = {};

            values.forEach((value: any) => {
                obj[value['label']] = value['value'];
            });

            let keys = Object.keys(obj);
            let valuesAux = Object.values(obj);


            setChartLabels(keys as string[]);
            setChartData(valuesAux as number[]);


        }
    }, []);

    const data = {
        labels: ['18-24 ans', '25-34 ans', '35-44 ans', '45-55 ans', '55+ ans'],
        datasets: [
            {
                label: 'Lots gagnés',
                data: chartData,
                backgroundColor: [
                    '#42B2FF',
                    '#E3E94B',
                    '#7BC558',
                    '#EBB3E6',
                    '#ff7b7b',
                ],
                borderColor: [
                    '#42B2FF',
                    '#E3E94B',
                    '#7BC558',
                    '#EBB3E6',
                    '#ff7b7b',
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
                        let percentage = ((value / chartData.reduce((a, b) => a + b, 0)) * 100).toFixed(2) + '%';
                        return `${label}: ${value} (${percentage})`;
                    },
                },
            },
        },
    };



    return <Pie  id="myChart" key={"chart-div"} options={options as any} className={`${styles.chartCircle} ${styles.chartCircleAge} `} data={data} />;
}
