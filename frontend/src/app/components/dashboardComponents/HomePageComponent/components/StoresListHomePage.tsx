import React, {useEffect, useState} from 'react';
import {Button, Col, Row, Select} from 'antd';
import {getStoresForAdmin} from "@/app/api";
import RedirectService from "@/app/service/RedirectService";
import LogoutService from "@/app/service/LogoutService";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {DownloadOutlined, MehOutlined, PlusOutlined, SearchOutlined} from "@ant-design/icons";

interface OptionType {
    city: string;
    postal_code: string;
    country: string;
    address: string;
    id: any;
    key: string;
    label: string;
    value: string;
    name: string;
    siren: string;
    status: string;
}

function StoresList({ globalSelectedStoreId, onSelectStore  }: {  globalSelectedStoreId: string; onSelectStore: (value: string) => void; }) {


    const [selectedStoreId, setSelectedStoreId] = useState<string>('');
    const onChange = (value: string) => {
        setSelectedStoreId(value);
        onSelectStore(value);
    };

    const onSearch = (value: string) => {
        console.log('search:', value);
    };

    const filterOption = (input: string, item: OptionType) => (item?.label ?? '').toLowerCase().includes(input.toLowerCase());

    const { logoutAndRedirectAdminsUserToLoginPage } = LogoutService();
    const [storesList, setStoresList] = useState<OptionType[]>([]);

    const [storesOptionsList, setStoresOptionsList] = useState<OptionType[]>([]);

    useEffect(() => {
        getStoresForAdmin().then((response) => {
            setStoresList(response.storesResponse);
        }).catch((err) => {
            if (err.response){
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            }else {
                console.log(err.request);
            }
        });
    }, []);

    useEffect(() => {
        if (globalSelectedStoreId) {
            setSelectedStoreId(globalSelectedStoreId);
        }
    }, [globalSelectedStoreId]);



    useEffect(() => {
        const options: OptionType[] = [];
        let allStoresOption = {
            key: 'allStores',
            label: 'Tous les magasins',
            value: '',
        };
        options.push(allStoresOption as OptionType);
        storesList.forEach((store , index) => {
            const option: { label: string; value: string; key: any } = {
                key: store.id,
                label: (index+1)+'- ' + store.name + " - " + store.city + " " + store.postal_code + (store.siren ? " ( "+ store.siren +" ) " : "" ) + (store.status=='2' ? ' ( Fermé )' : '') ,
                value: store.id,
            };
            options.push(option as OptionType);
        });
        setStoresOptionsList(options);
    }, [storesList]);



    return (
        <>
        <Row key={selectedStoreId} className={`${styles.centerElementRow} `}>
            <Select
                defaultValue={selectedStoreId ? selectedStoreId : "Veuillez choisir un magasin"}
                value={selectedStoreId? selectedStoreId : "Veuillez choisir un magasin"}
                className={`${styles.selectStoresOptions} dashboardStoresSelect`}
                showSearch
                placeholder="Veuillez choisir un magasin"
                optionFilterProp="children"
                onChange={onChange}
                onSearch={onSearch}
                filterOption={filterOption as any}
                options={storesOptionsList}
                notFoundContent={<div className={styles.selectNoResultFound}>
                    <p>Aucun magasin trouvé</p>
                    <p><MehOutlined /></p>
                </div>}
            />


        </Row>

        </>
    );
}

export default StoresList;
