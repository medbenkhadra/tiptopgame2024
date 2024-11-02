import React, {useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import LogoutService from "@/app/service/LogoutService";
import {LockOutlined, UserOutlined} from '@ant-design/icons';
import {Tabs} from 'antd';
import PersonalInformations
    from "@/app/components/dashboardComponents/GeneralSettingsComponents/components/PersonalInformations";


import SecuritySettings
    from "@/app/components/dashboardComponents/GeneralSettingsComponents/components/SecuritySettings";


const { TabPane } = Tabs;
function GeneralSettingsTemplates() {

    const [activeTab, setActiveTab] = useState('1');

    const handleTabChange = (key:any) => {
        setActiveTab(key);
    };

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [loading, setLoading] = useState(false);





    const itemsTabs= [
        {
            label: (
                <span className={`${styles.headerCorrespandancesTabs}`}>
            <UserOutlined className={`${styles.headerCorrespandancesTabsTcon}`} />
            Informations Générales
          </span>
            ),
            key: '1',
            children: <><PersonalInformations ></PersonalInformations></>,
        },
        {
            label: (
                <span className={`${styles.headerCorrespandancesTabs}`}>
            <LockOutlined className={`${styles.headerCorrespandancesTabsTcon}`} />
            Paramètres de Sécurité
          </span>
            ),
            key: '2',
            children: <><SecuritySettings></SecuritySettings></>,
        },


    ]


    return (
        <div className={styles.homePageContent}>

            <div className={`${styles.homePageContentTopHeader}`}>
                <h1 className={`mx-3`}>
                    Paramètres Généraux
                </h1>
                <div className={`${styles.ticketsCardsMain} mt-5`}>
                    <div className={`${styles.ticketsCardsDiv} ${styles.correspandancesDiv} mb-5 px-4`}>

                        <Tabs
                            className={`${styles.correspandancesTabs} correspandancesTabsAux`}
                            defaultActiveKey="1"
                            items={itemsTabs as any}
                            onChange={handleTabChange}
                            activeKey={activeTab}
                        >
                            {itemsTabs.map((tab) => (
                                <TabPane tab={tab.label} key={tab.key}>
                                    {tab.children}
                                </TabPane>
                            ))}
                        </Tabs>


                    </div>

                </div>
            </div>


        </div>
    );
}

export default GeneralSettingsTemplates;