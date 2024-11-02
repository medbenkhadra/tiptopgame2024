import React, {useEffect, useState} from 'react';
import {Form, Select} from 'antd';
import {getStoresForAdmin} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
const {Option} = Select;


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

function StoresList({ onSelectStore   }: { onSelectStore: (value: string) => void;}) {


    const [selectedStoreId, setSelectedStoreId] = useState<string>('');


    const onChange = (value: string) => {
        console.log(value);
        setSelectedStoreId(value);
        onSelectStore(value);

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
        const options: OptionType[] = [];
        storesList.forEach((store , index) => {
            const option: { label: string; value: string; key: any } = {
                key: store.id,
                label: (index+1)+"- " + store.name + (store.siren ? " ( "+ store.siren +" ) - " : " - " ) + store.city + " " + store.postal_code + (store.status=='2' ? ' ( Fermé )' : '') ,
                value: store.id,
            };
            options.push(option as OptionType);
        });
        setStoresOptionsList(options);
    }, [storesList]);

    const renderStores = () => {
        return storesOptionsList.map((store) => {
            return <Option key={store.key} value={store.value}>{store.label}</Option>
        })
    }

    return (
        <>
            <Form.Item
                className={`${styles.formItem} searchTicketFormItem mb-5`}
                name={`store`}
                label={`Magisin`}
                initialValue=""
            >
                <Select onChange={onChange} placeholder={`Tous les Magasins`} className={`mt-2`}>
                    <Option key={"default-id"} value={""}>Tous les Magasins</Option>
                    {renderStores()}
                </Select>
            </Form.Item>
        </>
    );
}

export default StoresList;
