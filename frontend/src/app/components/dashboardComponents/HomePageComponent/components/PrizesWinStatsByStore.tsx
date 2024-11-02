"use client";
import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {
    Chart as ChartJS,
    RadialLinearScale,
    ArcElement,
    Tooltip,
    Legend,
} from 'chart.js';
import { PolarArea } from 'react-chartjs-2';

ChartJS.register(RadialLinearScale, ArcElement, Tooltip, Legend);


export default function PrizesWinStatsByStore(dataChart: any) {

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
        labels: chartLabels,
        datasets: [
            {
                label: 'Lots gagnés',
                data: chartData,
                backgroundColor: [
                    'rgba(66, 178, 255, 0.6)',
                    'rgba(227, 233, 75, 0.6)',
                    'rgba(128, 83, 127, 0.6)',
                    'rgba(239, 54, 222, 0.6)',
                    'rgba(255, 85, 85, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(65, 255, 238, 0.6)',
                    'rgba(138, 80, 33, 0.6)',
                    'rgba(0, 255, 0, 0.6)',
                    'rgba(255, 255, 0, 0.6)',
                    'rgba(0, 255, 255, 0.6)',
                    'rgba(255, 0, 0, 0.6)',
                    'rgba(215, 241, 139, 0.6)',
                ],
                borderColor: [
                    '#42B2FF',
                    '#E3E94B',
                    '#80537f',
                    '#ef36de',
                    '#FF5555',
                    '#FF9F40',
                    '#41ffee',
                    '#8a5021',
                    '#00ff00',
                    '#ffff00',
                    '#00ffff',
                    '#ff0000',
                    '#d7f18b',
                ],
                borderWidth: 1,
            },
        ],
    };

    return <div className={`${styles.barChartDiv} mt-0`}>
        <PolarArea className={`${styles.barChartElement} mt-0`} data={data} />
    </div>;
}


