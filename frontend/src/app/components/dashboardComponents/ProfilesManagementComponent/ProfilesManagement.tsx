import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {Row , Col} from "antd";
import StoresList from "@/app/components/dashboardComponents/ProfilesManagementComponent/components/StoresList";
import ProfilesUsersTable from "@/app/components/dashboardComponents/ProfilesManagementComponent/components/ProfilesUsersTable";
import { Result } from 'antd';

function ProfilesManagement() {

    const [selectedStoreId, setSelectedStoreId] = useState<string>('');

    const [userRole , setUserRole] = useState<string | null>(null);
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
    }, []);
    const handleStoreChange = (value: string) => {
        setSelectedStoreId(value);
    };



        return (
            <div className={styles.topHeaderStoreManagementFullWidth} >
                <Row>
                    <Col className={`${styles.fullWidthElement} mt-3`} >
                        <h3 className={styles.topHeaderProfileManagementTitle} >Gestion des profils</h3>
                    </Col>



                        <Col className={`${styles.fullWidthElement} mb-5`} >
                            <StoresList onSelectStore={handleStoreChange} ></StoresList>
                        </Col>



                    {selectedStoreId && (
                        <Col className={styles.fullWidthElement} >
                            <ProfilesUsersTable selectedStoreId={selectedStoreId}></ProfilesUsersTable>
                        </Col>
                    )}

                    {!selectedStoreId && (
                        <Col className={styles.fullWidthElement} >
                            <Result
                                status="warning"
                                title="Veuillez selectionner un magasin pour voir les profils"
                                extra={
                                   <>
                                   </>
                                }
                            />
                        </Col>
                    )}

                </Row>
            </div>
        );

}

export default ProfilesManagement;