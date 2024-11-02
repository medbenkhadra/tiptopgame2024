import React, {useEffect, useState} from 'react';
import {Button, Col, Row, Select} from 'antd';
import {getCorrespandanceTemplates} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {MehOutlined, SyncOutlined} from "@ant-design/icons";


interface OptionType {
    label: string;
    id: string;
    title: string;
    description: string;
    type: string;
    service: string;
    name: string;
    variables: string[];
    value: string;
}
interface SeachParams {
    title?: string | null;
    type?: string | null;
}


const search: SeachParams = {
    title: '',
    type: '',
};
function EmailTemplateList({ onSelectTemplate , selectedTemplate  }: any) {


    const [selectedTemplateId, setSelectedTemplateId] = useState<string | undefined>(undefined);

    const [userRole , setUserRole] = useState<string | null>(null);
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
    }, []);


    const onChange = (value: string) => {
        console.log(value , "value");
        setSelectedTemplateId(value);
        onSelectTemplate(value);
    };

    useEffect(() => {
        setSelectedTemplateId(selectedTemplate);
        onSelectTemplate(selectedTemplate);
    }, [selectedTemplate]);


    const filterOption = (input: string, item: OptionType) => (item?.name ?? '').toLowerCase().includes(input.toLowerCase());
    const { logoutAndRedirectAdminsUserToLoginPage } = LogoutService();
    const [templatesList, setTemplatesList] = useState<OptionType[]>([]);
    const [templatesOptionsList, setTemplatesOptionsList] = useState<OptionType[]>([]);

    const [searchForm, setSearchForm] = useState<SeachParams>(search);


    const getTemplates = () => {
        getCorrespandanceTemplates(searchForm).then((res) => {
            let templates:any = [];
            res.forEach((template:any) => {
                templates.push(template);
            });
            setTemplatesList(templates);
        }).catch((err) => {
            console.log("err", err);
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            }
        });
    }

    useEffect(() => {
        getTemplates();
    }, []);


    useEffect(() => {
        const options: OptionType[] = [];
        templatesList.forEach((template , index) => {
            const option: { label: string; title: string; id: any , value:any } = {
                id: template.id,
                value: template.id,
                label: (template.id)+"- " + template.name  ,
                title: template.id,

            };
            options.push(option as OptionType);
        });
        setTemplatesOptionsList(options);
    }, [templatesList]);


    const onResetEditor = () => {
        setSelectedTemplateId(undefined);
        onSelectTemplate(undefined);
    };

    return (
        <>
            {userRole === 'ROLE_ADMIN' && (
                <>
                    <Row className={`d-flex justify-content-between`}>
                        <Col span={12} className={`d-flex justify-content-start`}>
                            <Button
                                className={`${styles.resetEmailTemplateForm} m-0` }
                                onClick={() => {
                                    onResetEditor();
                                }}
                            >
                                Réinitialiser l'editeur <SyncOutlined />
                            </Button>
                        </Col>
                        <Col span={12}>
                            <Select
                                className={`w-100`}
                                showSearch
                                placeholder="Veuillez choisir un modèle"
                                optionFilterProp="children"
                                onChange={onChange}
                                value={selectedTemplateId}
                                filterOption={filterOption as any}
                                options={templatesOptionsList}
                                notFoundContent={<div className={styles.selectNoResultFound}>
                                    <p>Aucun template trouvé</p>
                                    <p><MehOutlined /></p>
                                </div>}
                            />
                        </Col>




                    </Row>
                </>
            )}


        </>
    );
}

export default EmailTemplateList;
