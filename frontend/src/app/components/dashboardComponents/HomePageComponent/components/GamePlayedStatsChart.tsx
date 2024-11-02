"use client";
import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import {Line} from 'react-chartjs-2';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

export default function GamePlayedStatsChart(dataChart: any) {

    const[chartLabels, setChartLabels] = useState<string[]>([]);
    const[chartData, setChartData] = useState(null);

    useEffect(() => {

        if(dataChart){
            let values:any = Object.values(dataChart);


            let stats = values[0][0] as any;
            let statsAux = values[0][1] as any;
            let thirdStats = values[0][2] as any;


            if (stats) {
                let statsValues = Object.values(stats);
                let statsAuxValues  = Object.values(statsAux);
                let thirdStatsValues  = Object.values(thirdStats);

                let keys = Object.values(statsValues);
                let keysAux = Object.values(statsAuxValues);
                let keysThird = Object.values(thirdStatsValues);


                let tickets:any = [];
                let gains:any = [];
                let gainsAux:any = [];
                let thirdStatsAux:any = [];

                tickets = keys[1];
                gains = keysAux[1];
                gainsAux = keysAux[1];
                thirdStatsAux = keysThird[1];





                const ticketsKeys = Object.keys(tickets);
                const counts = Object.values(tickets);
                const countsAux = Object.values(gainsAux);
                const countsThird = Object.values(thirdStatsAux);


                ticketsKeys.reverse();
                countsThird.reverse();
                counts.reverse();
                countsAux.reverse();

                const finalData:any = [counts, countsAux,countsThird];


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
                text: 'Fréquence des participations',
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
                borderColor: '#42B2FF',
                backgroundColor: 'rgba(66,178,255,0.66)',
            },
            {
                label: 'Tour de roue joués',
                data: chartData ? chartData?.[1] : [0,0,0,0,0,0,0],
                borderColor: '#E3E94B',
                backgroundColor: 'rgba(227,233,75,0.66)',
            },
            {
                label: 'Cadeaux remportés',
                data: chartData ? chartData?.[2] : [0,0,0,0,0,0,0],
                borderColor: '#EBB3E6',
                backgroundColor: 'rgba(235,179,230,0.66)',
            },

        ],
    };

    return <div className={styles.barChartDiv}>
        <Line className={styles.barChartElement} options={options} data={data} />
    </div>;
}


