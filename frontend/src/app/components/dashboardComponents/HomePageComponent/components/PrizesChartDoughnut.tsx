"use client";
import React, {useEffect, useState} from 'react';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Doughnut } from 'react-chartjs-2';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

ChartJS.register(ArcElement, Tooltip, Legend);



export default function PrizesChartDoughunt(dataChart: any) {
    const inputRef = React.useRef<HTMLInputElement>(null);

    const [chartLabels, setChartLabels] = useState<string[]>([]);
    const [chartData, setChartData] = useState<number[]>([]);

    useEffect(() => {


        if(dataChart['dataChart']){
            let values = Object.values(dataChart['dataChart']);
            let array:any =[];

            values.forEach((value: any) => {
                array[value['label']] = value['value'];
            });

            let keys = Object.keys(array);
            let valuesAux = Object.values(array);

            setChartLabels(keys as string[]);
            setChartData(valuesAux as number[]);
        }



    }, []);

    const data = {
        labels: chartLabels,
        datasets: [
            {
                label: 'Lots gagnés',
                data: chartData,
                backgroundColor: [
                    '#42B2FF',
                    '#E3E94B',
                    '#7BC558',
                    '#EBB3E6',
                    '#FF5555',
                ],
                borderColor: [
                    '#42B2FF',
                    '#E3E94B',
                    '#7BC558',
                    '#EBB3E6',
                    '#FF5555',
                ],
                borderWidth: 1,
            },
        ],
    };


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



    return <Doughnut options={options as any} className={styles.chartCircle} data={data} ref={inputRef as any} />;
}
