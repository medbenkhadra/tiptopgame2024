import React, {useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import LogoutService from "@/app/service/LogoutService";
import {CodepenOutlined, HighlightOutlined, ToolOutlined} from '@ant-design/icons';
import {Tabs} from 'antd';
import SettingsCorrespandancesTemplates
    from "@/app/components/dashboardComponents/CorrespandancesTemplatesComponents/components/SettingsCorrespandancesTemplates";
import PersonalizeCorrespandancesTemplates
    from "@/app/components/dashboardComponents/CorrespandancesTemplatesComponents/components/PersonalizeCorrespandancesTemplates";
import ServicesCorrespandancesTemplates
    from "@/app/components/dashboardComponents/CorrespandancesTemplatesComponents/components/ServicesCorrespandancesTemplates";


const { TabPane } = Tabs;
function CorrespandancesTemplates() {

    const [activeTab, setActiveTab] = useState('1');

    const handleTabChange = (key:any) => {
        setActiveTab(key);
    };

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [loading, setLoading] = useState(false);


    const selectTemplate = (value: string) => {
        console.log(value, "value");
    }

    const [selectedTemplate, setSelectedTemplate] = useState<string | undefined>(undefined);

    const onSelectTemplate = (value: string) => {
        console.log(value);
        if(!value){
            console.log("reset form and selected in drop down");

        }
        setSelectedTemplate(value);
    }


    const itemsTabs= [
        {
            label: (
                <span className={`${styles.headerCorrespandancesTabs}`}>
            <CodepenOutlined className={`${styles.headerCorrespandancesTabsTcon}`} />
            Liste des Templates
          </span>
            ),
            key: '1',
            children: <><SettingsCorrespandancesTemplates selectTemplate={onSelectTemplate} selectTab={handleTabChange}></SettingsCorrespandancesTemplates></>,
        },
        {
            label: (
                <span className={`${styles.headerCorrespandancesTabs}`}>
            <HighlightOutlined className={`${styles.headerCorrespandancesTabsTcon}`} />
            Personnalisation
          </span>
            ),
            key: '2',
            children: <><PersonalizeCorrespandancesTemplates selectedTemplate={selectedTemplate} selectTemplate={onSelectTemplate}  selectTab={handleTabChange}></PersonalizeCorrespandancesTemplates></>,
        },

        {
            label: (
                <span className={`${styles.headerCorrespandancesTabs}`}>
            <ToolOutlined className={`${styles.headerCorrespandancesTabsTcon}`} />
            Services et Variables
          </span>
            ),
            key: '3',
            children: <><ServicesCorrespandancesTemplates selectTab={handleTabChange}></ServicesCorrespandancesTemplates></>,
        },

    ]


    return (
        <div className={styles.homePageContent}>

            <div className={`${styles.homePageContentTopHeader}`}>
                <h1 className={`mx-3`}>
                    Correspandances Templates E-mail
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

export default CorrespandancesTemplates;