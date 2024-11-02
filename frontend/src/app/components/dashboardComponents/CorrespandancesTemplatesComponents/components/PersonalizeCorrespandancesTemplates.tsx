import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import LogoutService from "@/app/service/LogoutService";
import dynamic from 'next/dynamic';
import EmailTemplateList
    from "@/app/components/dashboardComponents/CorrespandancesTemplatesComponents/components/widgets/EmailTemplateList";
import {Button, Checkbox, Col, Form, Input, Modal, Row, Select, Tag} from 'antd';
import {getEmailServices, getEmailTemplateById, updateEmailTemplate} from "@/app/api";
import {FastBackwardOutlined, SaveFilled} from "@ant-design/icons";

const CKEditorComponent = dynamic(() => import('@/app/components/dashboardComponents/CorrespandancesTemplatesComponents/components/widgets/CKEditorComponent'), { ssr: false });

interface PersonalizeCorrespandancesTemplatesProps {
    selectTab: (key: string) => void;
    selectTemplate: (value: string) => void;
    selectedTemplate: any;
}

const { Option } = Select;


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
    required: boolean;
}


interface DataTypeService {
    id: string;
    name: string;
    label: string;
    description: string;
}

function PersonalizeCorrespandancesTemplates({ selectTab , selectTemplate , selectedTemplate }: PersonalizeCorrespandancesTemplatesProps) {

    const [templateForm , setTemplateForm] = useState<OptionType>({
        label: '',
        id: '',
        title: '',
        description: '',
        type: '',
        service: '',
        name: '',
        variables: [],
        value: '',
        required: false,
    });


    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [loading, setLoading] = useState(false);


    const [editorContent, setEditorContent] = useState('');
    const handleEditorChange = (key:string ,content:string) => {
        setEditorContent(content);
    };

    useEffect(() => {
        setTemplateForm((prevState) => ({
            ...prevState,
            content: editorContent,
        }));
    }, [editorContent]);



    const [subjectEditorContent, setSubjectEditorContent] = useState('');
    const handleSubjectEditorChange = (ket:string ,content:string) => {
        setSubjectEditorContent(content);
    };

    useEffect(() => {
        setTemplateForm((prevState) => ({
            ...prevState,
            subject: subjectEditorContent,
        }));
    }, [subjectEditorContent]);



    const onFinish = (values: any) => {
        updateTemplate(values);
    }

    function updateTemplate(values: any) {
        if(!values){
            return;
        }

        Modal.confirm({
            title: 'Êtes-vous sûr de vouloir modifier ce modèle ?',
            content: 'Vous ne pourrez pas revenir en arrière',
            okText: 'Oui',
            cancelText: 'Non',
            onOk: () => {
                setLoading(true);
                updateEmailTemplate(templateForm.id , templateForm)
                    .then((res) => {
                        console.log(res);
                        setLoading(false);
                        Modal.success({
                            title: 'Succès',
                            content: 'Modèle modifié avec succès',
                        });
                    })
                    .catch((err) => {
                        Modal.error({
                            title: 'Erreur',
                            content: 'Erreur lors de la modification du modèle',
                        });
                        setLoading(false);
                        console.log("err", err);
                        if (err.response) {
                            if (err.response.status === 401) {
                                logoutAndRedirectAdminsUserToLoginPage();
                            }
                        }
                    });
            },
        });
    }


    function getTemplateById(id: string) {
        if (!id) {
            setTemplateForm({
                ...templateForm,
                id: '',
                title: '',
                description: '',
                type: '',
                service: '',
                name: '',
                variables: [],
                value: '',
                required: false,

            });
            setEditorContent('');
            setSubjectEditorContent('');
            return;
        }

        getEmailTemplateById(id)
            .then((res) => {
                console.log(res['title'], res, "res['content']");
                setTemplateForm({
                    ...templateForm,
                    id: res.id,
                    title: res.title,
                    description: res.description,
                    type: res.type,
                    service: res.service,
                    name: res.name,
                    variables: res.variables,
                    value: res.value,
                    required: res.required,
                });

                setEditorContent(res.content ?? '');
                setSubjectEditorContent(res.subject ?? '');
            })
            .catch((err) => {
                console.log("err", err);
                if (err.response) {
                    if (err.response.status === 401) {
                        logoutAndRedirectAdminsUserToLoginPage();
                    }
                }
            });
    }

    useEffect(() => {
        getTemplateById(selectedTemplate);
    }, [selectedTemplate]);


    useEffect(() => {
        console.log("personalize");
        console.log("templateFormtemplateForm ",templateForm , "templateForm");
    }, [templateForm]);


    const [servicesData, setServicesData] = useState<DataTypeService[]>();

    function getEmailServicesData() {
        getEmailServices().then((res) => {
            setServicesData(res);
            console.log("res.data", res);
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
        getEmailServicesData();
    }, []);

    function getServiceTagColor(name:string) {
        if(name.includes("client")){
            return "blue";
        }
        if(name.includes("employé")){
            return "green";
        }

        if(name.includes("store")){
            return "orange";
        }
        if(name.includes("reset")){
            return "red";
        }

        if(name.includes("ticket")){
            return "cyan";
        }


        return "purple";

    }


    function reloadForm() {
        setTemplateForm({
            ...templateForm,
            id: '',
            title: '',
            description: '',
            type: '',
            service: '',
            name: '',
            variables: [],
            value: '',
            required: false,

        });
        setEditorContent('');
        setSubjectEditorContent('');
        getTemplateById(selectedTemplate);

        Modal.success({
            title: 'Succès',
            content: 'Modèle réinitialisé avec succès',
        });
    }

    return (
        <>
            <EmailTemplateList selectedTemplate={selectedTemplate}  onSelectTemplate={selectTemplate}></EmailTemplateList>

            <div key={templateForm.id} className={`mt-4 w-100 ${styles.templatesPersoDiv}`}>
                <h2 className={`display-6 my-4`}>
                    Personnalisation du modèle
                </h2>

                {!selectedTemplate && (
                    <>
                        <p className={`text-danger`}>
                            Veuillez choisir un modèle à personnaliser
                        </p>
                    </>
                )}

                {selectedTemplate && (
                    <>
                        <Form
                            name="emailTemplateForm"
                            onFinish={onFinish}
                            layout="vertical"
                            key={templateForm.id}
                        >

                            <Row gutter={[16, 16]}>
                                <Col span={12}>
                                    <Form.Item initialValue={templateForm.name} label="Nom" name="name" required>
                                        <Input
                                            onChange={(e) => {
                                                setTemplateForm((prevState) => ({
                                                    ...prevState,
                                                    name: e.target.value,
                                                }));
                                            }}
                                            placeholder="Entrez le nom"
                                        />
                                    </Form.Item>
                                </Col>
                                <Col span={12}>
                                    <Form.Item initialValue={templateForm.title} label="Titre" name="title" required>
                                        <Input
                                            onChange={(e) => {
                                                setTemplateForm((prevState) => ({
                                                    ...prevState,
                                                    title: e.target.value,
                                                }));
                                            }}
                                            placeholder="Entrez le titre du modèle"
                                        />
                                    </Form.Item>
                                </Col>
                            </Row>



                            <Row gutter={16}>
                                <Col span={12} className={``}>
                                    <Form.Item className={`w-100`} initialValue={templateForm.description} required label="Description" name="description">
                                        <Input.TextArea
                                            onChange={(e) => {
                                                setTemplateForm((prevState) => ({
                                                    ...prevState,
                                                    description: e.target.value,
                                                }));
                                            }}
                                            readOnly={true}
                                            placeholder="Entrez la description" />
                                    </Form.Item>
                                </Col>

                                <Col span={12} className={``}>
                                    <Form.Item initialValue={templateForm.service}  required label="Service" name="service"
                                               validateStatus={templateForm.service === "" ? "error" : "success"}
                                               hasFeedback
                                               help={templateForm.service === "" ? "Veuillez entrer un service pour le template" : ""}
                                    >
                                        <Select
                                            disabled={true}
                                            onChange={(e) => {
                                                setTemplateForm((prevState) => ({
                                                    ...prevState,
                                                    service: e,
                                                }));
                                            }}
                                            value={templateForm.service}  placeholder="Sélectionnez le service">
                                            {servicesData?.map((service) => (
                                                <Select.Option value={service.id}>{service.label}</Select.Option>

                                            ))}
                                        </Select>
                                    </Form.Item>

                                    <Form.Item
                                        className={``}
                                        label="Type"
                                        name="type"
                                        rules={[{ required: true, message: 'Veuillez entrer un type pour le template' }]}
                                        help={templateForm.type === "" ? "Veuillez entrer un type pour le template" : ""}
                                        validateStatus={templateForm.type === "" ? "error" : "success"}
                                        hasFeedback
                                        initialValue={templateForm.type}
                                    >
                                        <Select
                                            onChange={(e) => {
                                                setTemplateForm((prevState) => ({
                                                    ...prevState,
                                                    type: e,
                                                }));
                                            }}
                                            value={templateForm.type}
                                        >
                                            <Select.Option value="direction">Direction</Select.Option>
                                            <Select.Option value="service">Service</Select.Option>
                                            <Select.Option value="ads">Publicités</Select.Option>
                                            <Select.Option value="other">Autre</Select.Option>
                                        </Select>
                                    </Form.Item>
                                </Col>

                            </Row>




                            <Row gutter={16}>
                                <Col span={12} className={`d-flex justify-content-start`}>
                                    <Form.Item label="Préféré" name="required" valuePropName="checked">
                                        <Checkbox
                                            onChange={(e) => {
                                                setTemplateForm((prevState) => ({
                                                    ...prevState,
                                                    required: e.target.checked,
                                                }));
                                            }}
                                            defaultChecked={templateForm.required ?? false}>
                                            Préféré
                                        </Checkbox>
                                    </Form.Item>
                                </Col>
                            </Row>

                            <Form.Item className={`mb-0`} label="Variables autorisées" name="variables">
                            </Form.Item>

                            {templateForm.variables?.map((variable:any) => (
                                <Tag
                                    key={variable.id}
                                    className={`${styles.serviceTag}  variable_tag`}
                                    color={getServiceTagColor(variable.name)}
                                >{variable.name}</Tag>
                            ))}
                            {!templateForm.variables.length && <Tag color="red">Aucune variable</Tag>}



                            <Form.Item required className={`d-flex flex-column w-100`} label="Objet" name="subject">
                                <CKEditorComponent key={templateForm.id} variables={templateForm.variables}  className={'my-5 d-flex flex-column w-100'} index={"subject"} content={subjectEditorContent} onChange={handleSubjectEditorChange} />
                            </Form.Item>

                            <Form.Item required label="Contenu" name="content">
                                <CKEditorComponent key={templateForm.id} variables={templateForm.variables} className={'my-5 d-flex flex-column w-100 content_template_editor'}  index={"content"} content={editorContent} onChange={handleEditorChange} />
                            </Form.Item>


                            <Row gutter={16} className={`d-flex justify-content-end`}>
                                <Col span={12} className={`d-flex justify-content-end`}>
                                    <Form.Item >
                                        <Button className={`mx-3 ${styles.cancelFormEmailTemplateBtn}`} type="default"
                                                onClick={() => {
                                                    reloadForm();
                                                }}
                                        >
                                            Annuler les modifications <FastBackwardOutlined />
                                        </Button>
                                    </Form.Item>
                                    <Form.Item>
                                        <Button className={`mx-3 ${styles.saveFormEmailTemplateBtn} saveFormEmailTemplateBtnGlobal`}  type="primary" htmlType="submit">
                                            Enregistrer <SaveFilled />
                                        </Button>
                                    </Form.Item>
                                </Col>
                            </Row>




                        </Form>
                    </>
                )}

            </div>

        </>
    );
}

export default PersonalizeCorrespandancesTemplates;