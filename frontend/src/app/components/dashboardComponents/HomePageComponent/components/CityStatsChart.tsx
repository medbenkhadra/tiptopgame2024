import React, {useEffect, useState} from 'react';
import {BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip} from 'chart.js';
import {Bar} from 'react-chartjs-2';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

  function CityStatsChart(dataChart: any) {

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
        plugins: {
            title: {
                display: true,
                text: 'Répartition des lots par ville',
            },
        },
        responsive: true,
        interaction: {
            mode: 'index' as const,
            intersect: false,
        },
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true,
            },
        },
    };

    const labels = chartLabels;

    const data = {
        labels,
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
                    '#FF9F40',
                    '#41ffee',
                    '#8a5021',
                    '#00ff00',
                    '#ffff00',
                    '#ff0000',
                    '#d7f18b',
                    '#ff7b7b',
                    '#8c00ff',
                    '#f550f8',

                ],
                borderColor: [
                    '#42B2FF',
                    '#E3E94B',
                    '#7BC558',
                    '#EBB3E6',
                    '#FF5555',
                    '#FF9F40',
                    '#41ffee',
                    '#8a5021',
                    '#00ff00',
                    '#ffff00',
                    '#ff0000',
                    '#d7f18b',
                    '#ff7b7b',
                    '#8c00ff',
                    '#f550f8',
                ],
                borderWidth: 1,
            },
        ],
    };

    return <div className={styles.barChartDiv}>
                    <Bar className={styles.barChartElement} options={options} data={data}/>
    </div>;
}

export default CityStatsChart

