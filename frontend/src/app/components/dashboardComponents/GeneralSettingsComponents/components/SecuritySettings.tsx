import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import LogoutService from "@/app/service/LogoutService";
import {Button, Col, Divider, Form, Input, message, Modal, Row, Tag} from "antd";
import {SaveFilled, StopOutlined, UnlockOutlined, WarningOutlined} from "@ant-design/icons";
import {getUserPersonalInfo, updateUserEmail, updateUserPassword} from "@/app/api";

interface OptionType {
    id: string;
    current_password: string;
    new_password: string;
    new_password_confirm: string;
    email: string;
    new_email: string;
}






function SecuritySettings() {


    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [loading, setLoading] = useState(false);
    const [visible, setVisible] = useState(false);
    const showModal = () => {
        setVisible(true);
    };

    const handleCancel = () => {
        setVisible(false);
    };

    const [personalInfoForm, setPersonalInfoForm] = useState({
                    id: "",
                    email: "",
                    role: "",
        });

    const [rerender, setRerender] = useState(false);

    function getPersonalInfo() {
        let loggedInUserId = localStorage.getItem('loggedInUserId');
        if (loggedInUserId == null) {
            logoutAndRedirectAdminsUserToLoginPage();
        }else{
            getUserPersonalInfo(loggedInUserId).then((response) => {
                setRerender(!rerender);
                if (response) {
                    setPersonalInfoForm(response.user);
                    setPasswordForm((prevState) => ({
                        ...prevState,
                        id: response.user.id,
                        email: response.user.email,
                        new_email: response.user.email,
                        current_password: "",
                        new_password: "",
                        new_password_confirm: "",
                    }));
                }
            }).catch((error) => {
                console.log(error);
                setRerender(!rerender);
                Modal.error({
                    title: 'Erreur lors de la récupération des informations personnelles',
                    content: <>
                        {error.response && (
                            <p>
                                {error.response.data.message}
                            </p>
                        )}
                    </>,
                })
            })
        }

    }
    useEffect(() => {
        getPersonalInfo();

    }, []);




    const [passwordForm, setPasswordForm] = useState<OptionType>({
        id: "",
        current_password: "",
        new_password: "",
        new_password_confirm: "",
        email: "",
        new_email: "",
    });


    function updateProfileInfo(values: any) {
        console.log('values:', values);
    }

    const onFinish = (values: any) => {
        //setPersonalInfoForm(values);
        updateProfileInfo(values);
    }


    useEffect(() => {
        console.log(passwordForm , 'passwordForm')
    }, [passwordForm]);


    function resetPassword() {

        if(passwordForm.new_password =='' || passwordForm.new_password_confirm =='' || passwordForm.current_password ==''){
            Modal.error({
                title: 'Erreur',
                content: <>
                    <p>
                        Veuillez saisir votre mot de passe actuel et votre nouveau mot de passe
                    </p>
                </>,
            });

            return;
        }

        if(passwordForm.new_password != passwordForm.new_password_confirm){
            Modal.error({
                title: 'Erreur',
                content: <>
                    <p>
                        Les mots de passe ne correspondent pas
                    </p>
                </>,
            });

            return;
        }

        if(passwordForm.new_password.length < 8){
            Modal.error({
                title: 'Erreur',
                content: <>
                    <p>
                        Le mot de passe doit contenir au moins 8 caractères
                    </p>
                </>,
            });

            return;
        }

        Modal.confirm({
            title: 'Êtes-vous sûr de vouloir réinitialiser votre mot de passe ?',
            content: 'Vous ne pourrez pas revenir en arrière',
            okText: 'Oui',
            cancelText: 'Non',
            onOk: () => {
                updateUserPassword(passwordForm.id , passwordForm).then((response) => {
                    if (response) {
                        Modal.success({
                            title: 'Mot de passe réinitialisé avec succès',
                            content: <>
                                <p>
                                    Votre mot de passe a été réinitialisé avec succès
                                </p>
                            </>,
                        });
                        setPasswordForm((prevState) => ({
                            ...prevState,
                            current_password: "",
                            new_password: "",
                            new_password_confirm: "",
                        }));

                        getPersonalInfo();
                    }
                }).catch((error) => {
                    console.log(error.response);
                    Modal.error({
                        title: 'Erreur lors de la réinitialisation du mot de passe',
                        content: <>
                            {error.response && (
                                        <p>
                                            {error.response.data.message}
                                        </p>

                            )}
                        </>,

                    })
                });
            },
        });

    }

    const [emailValidationStatus, setEmailValidationStatus] = useState('');
    const handleEmailChange = (e : any ) => {
        const newEmail = e.target?.value;



        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValidEmail = emailRegex.test(newEmail);

        if (isValidEmail) {
            setEmailValidationStatus('success');
        } else {
            setEmailValidationStatus('error');
        }

        setPasswordForm({ ...passwordForm, new_email: newEmail });
    };



    function updateEmail() {

        if(emailValidationStatus == 'error'){
            return;
        }

            if(passwordForm.new_email ==''){
                Modal.error({
                    title: 'Erreur',
                    content: <>
                        <p>
                            Veuillez saisir votre nouvel email
                        </p>
                    </>,
                });

                return;
            }

            if(passwordForm.new_email == passwordForm.email){
                Modal.info({
                    title: 'E-mail identique',
                    content: <>
                        <p>
                            Veuillez saisir un email différent de votre email actuel
                        </p>
                    </>,
                });

                return;
            }

            Modal.confirm({
                title: 'Êtes-vous sûr de vouloir modifier votre adresse e-mail ?',
                content: 'Vous ne pourrez pas revenir en arrière',
                okText: 'Oui',
                cancelText: 'Non',
                onOk: () => {
                    showModal();
                },
            });
    }


    const handleOk = () => {
        message.destroy();
        if(passwordForm.current_password ==''){
           message.error('Veuillez saisir votre mot de passe actuel');
           return;
        }

        if(passwordForm.current_password.length < 8){
            message.error('Le mot de passe doit contenir au moins 8 caractères');

            return;
        }

        updateUserEmail(passwordForm.id , passwordForm).then((response) => {
            if (response) {
                Modal.success({
                    title: 'Adresse e-mail modifiée avec succès',
                    content: <>
                        <p>
                            Votre adresse e-mail a été modifiée avec succès
                        </p>
                        <strong>
                            Veuillez vous reconnecter pour appliquer les changements
                        </strong>
                    </>,
                });


                setPasswordForm((prevState) => ({
                    ...prevState,
                    current_password: "",
                    new_password: "",
                    new_password_confirm: "",
                }));

                setTimeout(() => {
                    logoutAndRedirectAdminsUserToLoginPage();
                }, 3000);
            }
        }).catch((error) => {
            console.log(error.response);
            Modal.error({
                title: 'Erreur lors de la modification de l\'adresse e-mail',
                content: <>
                    {error.response && (
                        <p>
                            {error.response.data.message}
                        </p>

                    )}
                </>,

            })
        });
        setVisible(false);
        setPasswordForm((prevState) => ({
            ...prevState,
            current_password: "",
        }));

    };

    return (
        <>

            <div  className={`mt-4 w-100 ${styles.templatesPersoDiv}`}>
                <h2 className={`display-6 mt-5`}>
                    Paramètres de sécurité du compte
                </h2>

                <Row
                   className={`w-100`}
                >
                    <>
                        <Form
                            name="userInfo"
                            onFinish={onFinish}
                            layout="vertical"
                            key={`${personalInfoForm.id}-${rerender}`}
                            className={`w-100`}
                        >


                            <strong className={`my-5 d-flex justify-content-start`}>
                                Informations d'indentification
                            </strong>

                            <Row gutter={16}>
                                <Col span={12}>
                                    <Form.Item
                                        validateStatus={emailValidationStatus === 'error' ? 'error' : 'success'}
                                        hasFeedback={emailValidationStatus === 'error'}
                                        initialValue={passwordForm.new_email} label="Identifiant E-mail" name="id" required>
                                        <Input
                                            onChange={(e) => {
                                                handleEmailChange(e);
                                            }}
                                            placeholder="Entrez votre adresse e-mail"

                                        />
                                    </Form.Item>
                                </Col>
                                <Col span={12} className={`w-100 d-flex align-items-center justify-content-end `} >
                                    {personalInfoForm.role && (
                                        <>
                                            {personalInfoForm.role === 'ROLE_ADMIN' && (
                                                <Tag color="blue">Administrateur TipTop</Tag>
                                            )}

                                            {personalInfoForm.role === 'ROLE_STOREMANAGER' && (
                                                <Tag color="green">Manager de magasin</Tag>
                                            )}

                                            {personalInfoForm.role === 'ROLE_EMPLOYEE' && (
                                                <Tag color="orange">Employé ( caissier )
                                                </Tag>
                                            )}

                                            {personalInfoForm.role === 'ROLE_CLIENT' && (
                                                <Tag color="purple">Client - participant</Tag>
                                            )}

                                            {personalInfoForm.role === 'ROLE_ANONYMOUS' && (
                                                <Tag color="pink">
                                                    Client - non participant (Anonyme)
                                                </Tag>
                                            )}

                                        </>
                                    )}


                                </Col>
                            </Row>

                            <Row gutter={16} className={`d-flex justify-content-start`}>
                                <Col span={12} className={`d-flex justify-content-end`}>
                                    <Form.Item>
                                        <Button
                                            onClick={() => {
                                                updateEmail();
                                            }}

                                            className={`${styles.saveFormEmailTemplateBtn} saveFormEmailTemplateBtnGlobal`}  type="primary" htmlType="submit">
                                            Modifier <SaveFilled />
                                        </Button>
                                    </Form.Item>
                                </Col>
                            </Row>

                            <Divider />

                            <strong className={`my-5 d-flex justify-content-start`}>
                               Réinitialisation du mot de passe
                            </strong>

                            <Row gutter={16}>
                                <Col span={12}>
                                    <Form.Item label="Mot de passe actuel" name="currentPassfffword" required>
                                        <Input.Password
                                            autoComplete={'off'}
                                            value={passwordForm.current_password}
                                            onChange={(e) => {
                                               setPasswordForm({...passwordForm, current_password: e.target.value})
                                            }}
                                            placeholder="Entrez votre mot de passe actuel"


                                        />
                                    </Form.Item>
                                </Col>
                            </Row>
                            <Row gutter={16}>
                                <Col span={12}>
                                    <Form.Item label="Nouveau mot de passe" name="newPassword" required>
                                        <Input.Password
                                            value={passwordForm.new_password}
                                            onChange={(e) => {
                                                setPasswordForm({...passwordForm, new_password: e.target.value});
                                            }}
                                            placeholder="Entrez votre nouveau mot de passe"
                                        />
                                    </Form.Item>
                                </Col>

                                <Col span={12}>
                                    <Form.Item label="Répetez le nouveau mot de passe" name="newPasswordConfirm" required>
                                        <Input.Password
                                            value={passwordForm.new_password_confirm}
                                            onChange={(e) => {
                                                setPasswordForm({...passwordForm, new_password_confirm: e.target.value});
                                            }}
                                            placeholder="Entrez votre nouveau mot de passe"
                                        />
                                    </Form.Item>
                                </Col>

                            </Row>

                            <Row gutter={16} className={`d-flex justify-content-end`}>
                                <Col span={12} className={`d-flex justify-content-end`}>

                                    <Form.Item
                                    className={`m-0 p-0`}
                                    >
                                        <Button
                                            onClick={() => {
                                                resetPassword();
                                            }}
                                            className={`${styles.saveFormEmailTemplateBtn} saveFormEmailTemplateBtnGlobal`}  type="primary">
                                           Réinitialiser <UnlockOutlined />
                                        </Button>
                                    </Form.Item>
                                </Col>
                            </Row>

                        </Form>


                        <Divider />

                        <Row gutter={16} className={`d-flex justify-content-start w-100 m-0 p-0 mb-5`}>
                            <Col span={12} className={`d-flex justify-content-start w-100 m-0 p-0`}>
                                <Form.Item
                                className={`m-0 p-0 w-100`}
                                >
                                    <Button
                                        onClick={() => {

                                        }}

                                        className={`${styles.disableAccountBtn}   disableAccountBtn `}  type="primary" htmlType="submit">
                                        Désactiver mon compte <StopOutlined />
                                    </Button>
                                </Form.Item>
                                <Form.Item
                                    className={`m-0 p-0 w-100`}
                                >
                                    <Button
                                        onClick={() => {

                                        }}

                                        className={`${styles.deleteAccountBtn} mx-3  deleteAccountBtn `}  type="primary" htmlType="submit">
                                        Supprimer mon compte <WarningOutlined />
                                    </Button>
                                </Form.Item>
                            </Col>
                        </Row>


                    </>
                </Row>

            </div>


            <Modal
                title="Êtes-vous sûr de vouloir modifier votre adresse e-mail ?"
                visible={visible}
                onOk={handleOk}
                onCancel={handleCancel}
                okText="Confirmer"
                cancelText="Non"
                width={400}
            >
                <p>Vous ne pourrez pas revenir en arrière</p>
                <Input
                    type="password"
                    placeholder="Entrez votre mot de passe"
                    value={passwordForm.current_password}
                    onChange={(e) => {
                        setPasswordForm({...passwordForm, current_password: e.target.value});
                    }}
                />
            </Modal>

        </>
    );
}

export default SecuritySettings;