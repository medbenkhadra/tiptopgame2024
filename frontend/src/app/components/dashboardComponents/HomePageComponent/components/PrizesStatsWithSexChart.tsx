"use client";
import React, {useEffect,useState} from 'react';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';
import { Bar } from 'react-chartjs-2';

import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

export default function PrizesStatsWithSexChart(dataChart: any) {


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



    const options = {
        indexAxis: 'y' as const,
        elements: {
            bar: {
                borderWidth: 2,
            },
        },
        responsive: true,
        plugins: {
            legend: {
                position: 'top' as const,
            },
            title: {
                display: true,
                text: 'Analyse des lots par sexe',
            },
        },
    };

    const labels = chartLabels;

     const data = {
        labels,
        datasets: [
            {
                label: 'Hommes',
                data: chartData.map((item:any) => item.homme),
                borderColor: '#42B2FF',
                backgroundColor: '#42B2FF',
            },
            {
                label: 'Femmes',
                data: chartData.map((item:any) => item.femme),
                borderColor: '#EBB3E6',
                backgroundColor: '#EBB3E6',
            },
        ],
    };

    return <div className={styles.barChartDivSexStats}>
        <Bar options={options} data={data} />
    </div>;
}


