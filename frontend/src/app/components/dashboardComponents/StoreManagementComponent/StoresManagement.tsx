import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import StoreCards from "@/app/components/dashboardComponents/StoreManagementComponent/components/storeCards";
import {Row , Col} from "antd";
import StoresList from "@/app/components/dashboardComponents/StoreManagementComponent/components/StoresList";
import StoreDataInfoTable from "@/app/components/dashboardComponents/StoreManagementComponent/components/StoreDataInfoTable";
function StoresManagement() {

    const [selectedStoreId, setSelectedStoreId] = useState<string>('');
    const [isStoresUpdated, setIsStoresUpdated] = useState(false);

    const handleStoreChange = (value: string) => {
        setSelectedStoreId(value);
    };

    const [userRole , setUserRole] = useState<string | null>(null);
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
    }, []);



        return (
            <div className={styles.topHeaderStoreManagementFullWidth} >
                {userRole === 'ROLE_ADMIN' && (
                    <>
                <Row>
                    <Col className={`px-4 mt-4 ${styles.homePageContent}`} >
                        <h2>Gestion des magasins</h2>
                    </Col>
                </Row>
                <Row>
                    <Col className={styles.topHeaderStoreManagementFullWidth} >
                        <StoreCards isStoresUpdated={isStoresUpdated} ></StoreCards>
                    </Col>
                </Row>
                <Row>
                    <Col className={styles.fullWidthElement} >
                        <StoresList globalSelectedStoreId={selectedStoreId} onSelectStore={handleStoreChange}  isStoresUpdated={isStoresUpdated} onStoreUpdate={() => setIsStoresUpdated(!isStoresUpdated)} ></StoresList>
                    </Col>
                </Row>
                    </>
                )}

                {userRole === 'ROLE_STOREMANAGER' && (
                    <>
                        <Row>
                            <Col className={`px-4 mt-4 ${styles.homePageContent}`} >
                                <h2>Gestion de magasin</h2>
                            </Col>
                        </Row>
                        <Row>
                            <Col className={styles.fullWidthElement} >
                                <StoresList globalSelectedStoreId={selectedStoreId} onSelectStore={handleStoreChange}  isStoresUpdated={isStoresUpdated} onStoreUpdate={() => setIsStoresUpdated(!isStoresUpdated)} ></StoresList>
                            </Col>
                        </Row>
                    </>
                )}

                <Row>
                    <Col className={`${styles.fullWidthElement} mx-5 mt-5`} >
                        <StoreDataInfoTable changeSelectedStore={()=> {
                            handleStoreChange(selectedStoreId)}}  isStoresUpdated={isStoresUpdated} onStoreUpdate={() => setIsStoresUpdated(!isStoresUpdated)} key={selectedStoreId} selectedStoreId={selectedStoreId}></StoreDataInfoTable>
                    </Col>
                </Row>
            </div>
        );

}

export default StoresManagement;