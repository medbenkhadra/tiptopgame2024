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

export default function PrizesCostTendance(dataChart: any) {

    const[chartLabels, setChartLabels] = useState<string[]>([]);
    const[chartData, setChartData] = useState(null);

    useEffect(() => {

        if(dataChart){
            let values:any = Object.values(dataChart);


            let stats = values[0][0] as any;
            let statsAux = values[0][1] as any;


            if (stats) {
                let statsValues = Object.values(stats);
                let statsAuxValues  = Object.values(statsAux);

                let keys = Object.values(statsValues);
                let keysAux = Object.values(statsAuxValues);


                let tickets:any = [];
                let gains:any = [];
                let gainsAux:any = [];

                tickets = keys[1];
                gains = keysAux[1];
                gainsAux = keysAux[1];





                const ticketsKeys = Object.keys(tickets);
                const counts = Object.values(tickets);
                const countsAux = Object.values(gainsAux);


                ticketsKeys.reverse();
                counts.reverse();
                countsAux.reverse();

                const finalData:any = [counts, countsAux];


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
                text: 'Evolution des coûts des cadeaux joués et recupérés (en €)',
            },
            tooltip: {
                callbacks: {
                    label: (context:any) => {
                        const label = context.dataset.label || '';
                        const value = context.parsed.y || 0;
                        const euroValue = value.toLocaleString('fr-FR', {
                            style: 'currency',
                            currency: 'EUR',
                            minimumFractionDigits: 2,
                        });

                        return `${label}: ${euroValue}`;
                    },
                },
            },
        },
    };

    const labels = chartLabels;
    const data = {
        labels,
        datasets: [
            {
                fill: true,
                label: 'Coût des cadeaux joués',
                data: chartData ? chartData?.[0] : [0,0,0,0,0,0,0],
                borderColor: '#42B2FF',
                backgroundColor: 'rgba(66,178,255,0.66)',
            },
            {
                fill: true,
                label: 'Coût des cadeaux récupérés',
                data: chartData ? chartData?.[1] : [0,0,0,0,0,0,0],
                borderColor: '#EBB3E6',
                backgroundColor: 'rgba(235,179,230,0.66)',
            },

        ],
    };

    return <div className={styles.barChartDiv}>
        <Line className={styles.barChartElement} options={options} data={data} />
    </div>;
}


