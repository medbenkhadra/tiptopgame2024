"use client";
import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';
import { Line } from 'react-chartjs-2';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

export default function GameStatusesTendanceStatsChart(dataChart: any) {

    const[chartLabels, setChartLabels] = useState<string[]>([]);
    const[chartData, setChartData] = useState(null);

    useEffect(() => {

        if(dataChart){
            let values:any = Object.values(dataChart);


            let stats = values[0][0] as any;
            let statsAux = values[0][1] as any;
            let thirdStats = values[0][2] as any;
            let cancelledStats = values[0][3] as any;
            let expiredStats = values[0][4] as any;



            if (stats) {
                let statsValues = Object.values(stats);
                let statsAuxValues  = Object.values(statsAux);
                let thirdStatsValues  = Object.values(thirdStats);
                let cancelledStatsValues  = Object.values(cancelledStats);
                let expiredStatsValues  = Object.values(expiredStats);



                let keys = Object.values(statsValues);
                let keysAux = Object.values(statsAuxValues);
                let keysThird = Object.values(thirdStatsValues);
                let keysCancelled = Object.values(cancelledStatsValues);
                let keysExpired = Object.values(expiredStatsValues);


                let tickets:any = [];
                let gains:any = [];
                let gainsAux:any = [];
                let thirdStatsAux:any = [];
                let cancelledStatsAux:any = [];
                let expiredStatsAux:any = [];


                tickets = keys[1];
                gains = keysAux[1];
                gainsAux = keysAux[1];
                thirdStatsAux = keysThird[1];
                cancelledStatsAux = keysCancelled[1];
                expiredStatsAux = keysExpired[1];





                const ticketsKeys = Object.keys(tickets);
                const counts = Object.values(tickets);
                const countsAux = Object.values(gainsAux);
                const countsThird = Object.values(thirdStatsAux);
                const countsCancelled = Object.values(cancelledStatsAux);
                const countsExpired = Object.values(expiredStatsAux);


                ticketsKeys.reverse();
                countsThird.reverse();
                counts.reverse();
                countsAux.reverse();
                countsCancelled.reverse();
                countsExpired.reverse();

                const finalData:any = [counts, countsAux,countsThird, countsCancelled, countsExpired];

                setChartLabels(ticketsKeys as string[]);
                setChartData(finalData);
            }











        }
    }, []);

    const options = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top' as const,
            },
            title: {
                display: true,
                text: 'Tendance des tickets',
            },
        },
    };

    const labels = chartLabels;
    const data = {
        labels,
        datasets: [
            {
                label: 'Tickets imprimés',
                data: chartData ? chartData?.[0] : [0,0,0,0,0,0,0],
                borderColor: '#42b2ff',
                backgroundColor: 'rgba(66,178,255,0.5)',
            },
            {
                label: 'En attente de validation',
                data: chartData ? chartData?.[1] : [0,0,0,0,0,0,0],
                borderColor: '#E3E94B',
                backgroundColor: '#E3E94B',
            },
            {
                label: 'Cadeaux remportés',
                data: chartData ? chartData?.[2] : [0,0,0,0,0,0,0],
                borderColor: '#7BC558',
                backgroundColor: 'rgba(123,197,88,0.53)',
            },
            {
                label: 'Annulés',
                data: chartData ? chartData?.[3] : [0,0,0,0,0,0,0],
                borderColor: '#FF5555',
                backgroundColor: '#fc8484',
            },
            {
                label: 'Expirés',
                data: chartData ? chartData?.[4] : [0,0,0,0,0,0,0],
                borderColor: '#8F0000',
                backgroundColor: 'rgba(143,0,0,0.5)',
            },
        ],
    };

    return <div className={styles.barChartDiv}>
        <Line className={styles.barChartElement} options={options} data={data} />
    </div>;
}


