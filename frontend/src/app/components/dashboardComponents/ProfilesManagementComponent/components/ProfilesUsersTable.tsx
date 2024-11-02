import React , {useState , useEffect} from 'react';
import {Col, Row, Table, TabsProps} from 'antd';
import { Tabs } from 'antd';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import StoreManagerTable from "@/app/components/dashboardComponents/ProfilesManagementComponent/components/profilesTabs/StoreManagerTable";
import {getAllProfilesByStoreId} from "@/app/api";
import {Simulate} from "react-dom/test-utils";
import error = Simulate.error;



interface storeUsersTableProps {
    selectedStoreId:string
}
function ProfilesUsersTable({selectedStoreId}: storeUsersTableProps) {


    const [activeTab, setActiveTab] = useState<string>('1');

    const onTabChange = (key: string) => {
        console.log(key);
        setActiveTab(key);
    };

    const [clientsCount , setClientsCount] = useState<number>(0);
    const [employeesCount , setEmployeesCount] = useState<number>(0);
    const [managersCount , setManagersCount] = useState<number>(0);

    useEffect(() => {
        getAllProfilesByStoreId(selectedStoreId).then((response) => {
            console.log("response tabssss :",response);
            setClientsCount(response.storeClientUsersCount);
            setEmployeesCount(response.storeEmployeeUsersCount);
            setManagersCount(response.storeManagerUsersCount);
        }).catch((error) => {

        })

    }, [selectedStoreId]);

    useEffect(() => {
        getAllProfilesByStoreId(selectedStoreId).then((response) => {
            console.log("response tabssss :",response);
            setClientsCount(response.storeClientUsersCount);
            setEmployeesCount(response.storeEmployeeUsersCount);
            setManagersCount(response.storeManagerUsersCount);
        }).catch((error) => {

        })

    }, []);

    const onUsersTableUpdate = () => {
        getAllProfilesByStoreId(selectedStoreId).then((response) => {
            console.log("response tabssss :",response);
            setClientsCount(response.storeClientUsersCount);
            setEmployeesCount(response.storeEmployeeUsersCount);
            setManagersCount(response.storeManagerUsersCount);
        }).catch((error) => {

        })
    }

    return (
        <Row className={`${styles.fullWidthElement}`}>
            <Col className={`${styles.fullWidthElement} px-5`}>
                <Tabs activeKey={activeTab} onChange={onTabChange}>
                    <Tabs.TabPane key='1' tab={`Managers ( ${managersCount} )`}>
                        {activeTab === '1' && <StoreManagerTable onUpdate={onUsersTableUpdate} roleKey={activeTab} selectedStoreId={selectedStoreId} profilesRole={"ROLE_STOREMANAGER"} />}
                    </Tabs.TabPane>
                    <Tabs.TabPane key='2' tab={`Employés ( ${employeesCount} )`} >
                        {activeTab === '2' && <StoreManagerTable onUpdate={onUsersTableUpdate} roleKey={activeTab} selectedStoreId={selectedStoreId} profilesRole={"ROLE_EMPLOYEE"} />}
                    </Tabs.TabPane>
                    <Tabs.TabPane key='3' tab={`Clients ( ${clientsCount} )`}  >
                        {activeTab === '3' && <StoreManagerTable onUpdate={onUsersTableUpdate} roleKey={activeTab} selectedStoreId={selectedStoreId} profilesRole={"ROLE_CLIENT"} />}
                    </Tabs.TabPane>
                </Tabs>
            </Col>
        </Row>
    );
}

export default ProfilesUsersTable;