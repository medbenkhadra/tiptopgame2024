"use client";
import React, {useEffect, useState} from 'react';

import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

import {ConfigProvider, Table} from 'antd';
import type { ColumnsType, TableProps } from 'antd/es/table';
import {StopOutlined} from "@ant-design/icons";
import frFR from "antd/lib/locale/fr_FR";

interface DataType {
    key: number;
    username: string;
    tickets: number;
    gains: number;
    amount: string;
    level: number;
}
export default function TopTeenParticipants(dataTable: any) {

    const[chartLabels, setChartLabels] = useState<string[]>([]);
    const[chartData, setChartData] = useState<number[]>([]);

    useEffect(() => {

        if(dataTable){

            let obj: any = {};

            dataTable["dataTable"].forEach((value: any) => {
                obj[value['label']] = value['value'];
            });

            let keys = Object.keys(obj);
            let valuesAux = Object.values(obj);


            setChartLabels(keys as string[]);
            setChartData(valuesAux as number[]);


        }
    }, []);


    const columns: ColumnsType<DataType> = [
        {
            title: 'Classement',
            dataIndex: 'key',
            sorter: {
                compare: (a, b) => a.key - b.key,
                multiple: 1,
            },
        },
        {
            title: 'Name',
            dataIndex: 'username',
        },
        {
            title: 'Tickets récoltés',
            dataIndex: 'tickets',
            sorter: {
                compare: (a, b) => a.tickets - b.tickets,
                multiple: 3,
            },
        },
        {
            title: 'Gagnant',
            dataIndex: 'gains',
            render: (gains: number) => {
                return <>
                    <span>
                        {gains} Fois
                    </span>
                </>
            },
            sorter: {
                compare: (a, b) => a.gains - b.gains,
                multiple: 2,
            },
        },
        {
            title: 'Points de fidélité',
            dataIndex: 'level',
            render: (level: number) => {
                return <>
                    <span>
                        {level} Points
                    </span>
                </>
            },
            sorter: {
                compare: (a, b) => a.level - b.level,
                multiple: 2,
            },
        },

    ];

    const data: DataType[] = chartData as any;

    const customEmptyText = (
        <div className={styles.emptyTableTextDiv}>
            <span>Aucun Historique trouvé
            </span>
            <span><StopOutlined/></span>
        </div>
    );


    const onChange: TableProps<DataType>['onChange'] = (pagination, filters, sorter, extra) => {
        console.log('params', pagination, filters, sorter, extra);
    };
    return <div className={styles.tableStats}>
        <ConfigProvider locale={frFR}>
        <Table columns={columns} dataSource={data} onChange={onChange}
               locale={{emptyText: customEmptyText}}

        />
        </ConfigProvider>
    </div>;
}


