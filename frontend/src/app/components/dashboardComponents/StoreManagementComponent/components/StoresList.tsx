import React, {useEffect, useState} from 'react';
import {Button, Col, Row, Select} from 'antd';
import {getStoresForAdmin , getStoreForStoreManager} from "@/app/api";
import RedirectService from "@/app/service/RedirectService";
import LogoutService from "@/app/service/LogoutService";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {DownloadOutlined, MehOutlined, PlusOutlined, SearchOutlined} from "@ant-design/icons";
import ModalAddOrUpdateStore
    from "@/app/components/dashboardComponents/StoreManagementComponent/components/ModalAddOrUpdateStore";

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

function StoresList({ globalSelectedStoreId, onSelectStore , isStoresUpdated , onStoreUpdate  }: {  globalSelectedStoreId: string; onSelectStore: (value: string) => void; isStoresUpdated: boolean;onStoreUpdate: () => void;}) {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const [userRole , setUserRole] = useState<string | null>(null);
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
    }, []);
    const showAddStoreModal = () => {
        setIsModalOpen(true);
    };

    const closeAddStoreModal = () => {
        setIsModalOpen(false);
    };

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
        if(userRole === 'ROLE_ADMIN'){
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
        }else if (userRole === 'ROLE_STOREMANAGER'){
            getStoreForStoreManager().then((response) => {
                setStoresList(response.storesResponse);

                setSelectedStoreId(response.storesResponse[0].id);
                onSelectStore(response.storesResponse[0].id);

            }).catch((err) => {
                if (err.response){
                    if (err.response.status === 401) {
                        logoutAndRedirectAdminsUserToLoginPage();
                    }
                }else {
                    console.log(err.request);
                }
            });
        }

    }, [userRole]);

    useEffect(() => {
        if (globalSelectedStoreId) {
            setSelectedStoreId(globalSelectedStoreId);
        }
    }, [globalSelectedStoreId]);



    useEffect(() => {
        const options: OptionType[] = [];
        storesList.forEach((store , index) => {
            const option: { label: string; value: string; key: any } = {
                key: store.id,
                label: (index+1)+'- ' + store.name + (store.siren ? " ( "+ store.siren +" ) - " : " - " ) + store.city + " " + store.postal_code + (store.status=='2' ? ' ( Fermé )' : '') ,
                value: store.id,
            };
            options.push(option as OptionType);
        });
        setStoresOptionsList(options);
    }, [storesList,isStoresUpdated]);

    useEffect(() => {
        if (isStoresUpdated) {
            setSelectedStoreId(selectedStoreId);
        }
    }, [isStoresUpdated]);

    return (
        <>
        <Row key={selectedStoreId} className={`${styles.centerElementRow} storeManageList `}>
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

            {userRole === 'ROLE_ADMIN' && (
                <>
                    <Button className={styles.addNewStoreBtn} onClick={showAddStoreModal}  icon={<PlusOutlined />} >
                        Ajouter un nouveau magasin
                    </Button>
                </>
            )}

        </Row>
            <ModalAddOrUpdateStore  changeSelectedStore={onSelectStore} onStoreUpdate={onStoreUpdate} storeId={null} modalIsOpen={isModalOpen} closeModal={closeAddStoreModal} updateStore={false} ></ModalAddOrUpdateStore>

        </>
    );
}

export default StoresList;
