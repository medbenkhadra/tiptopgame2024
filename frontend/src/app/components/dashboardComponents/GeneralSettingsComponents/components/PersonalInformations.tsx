import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import LogoutService from "@/app/service/LogoutService";
import {Button, Col, Divider, Form, Input, message, Modal, Row, Tag} from "antd";
import {FastBackwardOutlined, SaveFilled, SendOutlined} from "@ant-design/icons";
import {getUserPersonalInfo, sendActivationAccountEmailForUser, updateUserProfileInfo, uploadAvatar} from "@/app/api";
import AvatarUploader
    from "@/app/components/dashboardComponents/GeneralSettingsComponents/components/widgets/AvatarUploader";

const domain = process.env.NEXT_PUBLIC_DOMAIN_NAME;

interface OptionType {
    id: string;
    lastname: string;
    firstname: string;
    email: string;
    role: string;
    phone: string;
    address: string;
    city: string;
    postalCode: string;
    country: string;
    dateOfBirth: string;
    store: {
        id: string;
        name: string;
        address: string;
        city: string;
        postal_code: string;
        country: string;
        phone: string;
        email: string;
    };

    is_activated: boolean;
    is_activated_at: {
        date: string;
        time: string;
    };

    created_at: {
        date: string;
        time: string;
    };

    updated_at: {
        date: string;
        time: string;
    };
    avatar:{
        id: string;
        filename: string;
        path: string;
    };
    avatar_image:string;
}
function PersonalInformations() {


    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [loading, setLoading] = useState(false);
    const [rerender, setRerender] = useState(0);
    const [personalInfoForm, setPersonalInfoForm] = useState({
                    id: "",
                    lastname: "",
                    firstname: "",
                    email: "",
                    role: "",
                    phone: "",
                    address: "",
                    city: "",
                    postalCode: "",
                    country: "",
                    store: {
                        id: "",
                        name: "",
                        address: "",
                        city: "",
                        postal_code: "",
                        country: "",
                        phone: "",
                        email: "",
                    },

        is_activated: false,
                    is_activated_at: {
                        date: "",
                        time: "",
                    },

                    created_at: {
                        date: "",
                        time: "",
                    },

                    updated_at: {
                        date: "",
                        time: "",
                    },
            dateOfBirth: "",
        avatar:{
            id: "",
            filename: "",
            path: "",
        },
        avatar_image:"",
        });

    function getPersonalInfo() {
        let loggedInUserId = localStorage.getItem('loggedInUserId');
        if (loggedInUserId == null) {
            logoutAndRedirectAdminsUserToLoginPage();
        }else{
            getUserPersonalInfo(loggedInUserId).then((response) => {
                if (response) {
                    setPersonalInfoForm(response.user);
                }
            }).catch((error) => {
                console.log(error);
            })
        }

    }

    useEffect(() => {
        getPersonalInfo();

    }, []);

    useEffect(() => {
        console.log('personalInfoFormpersonalInfoFormpersonalInfoForm:', personalInfoForm);

    }, [personalInfoForm]);




    function updateProfileInfo(values: any) {
        Modal.confirm({
            title: 'Êtes-vous sûr de vouloir modifier vos informations personnelles ?',
            content: 'Vous ne pourrez pas revenir en arrière',
            okText: 'Oui',
            cancelText: 'Non',
            onOk: () => {
                console.log(values);
                let id = personalInfoForm.id;
                updateUserProfileInfo(id, values).then((response) => {
                    console.log('response:', response);
                    message.success('Vos informations personnelles ont été modifiées avec succès.');
                }).catch((error) => {
                    console.log('error:', error);
                    message.error('Une erreur est survenue lors de la modification de vos informations personnelles. Veuillez réessayer plus tard.');
                });
            },
        });
    }

    const onFinish = (values: any) => {
        //setPersonalInfoForm(values);
        updateProfileInfo(values);
        setRerender(rerender + 1);
    }


    const reloadForm = () => {
        getPersonalInfo();
        setRerender(rerender + 1);
        Modal.info({
            title: 'Formulaire réinitialisé',
            content: 'Le formulaire a été réinitialisé avec succès.',
        });
    }


    const sendActivationAccountEmail = () => {

        sendActivationAccountEmailForUser(personalInfoForm.id).then((response) => {
            console.log('response:', response);
            Modal.success({
                title: 'Email envoyé',
                content: 'Un email de vérification a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception et cliquer sur le lien de vérification pour vérifier votre compte.',
            });
        }).catch((error) => {
            console.log('error:', error);
            Modal.error({
                title: 'Erreur',
                content: 'Une erreur est survenue lors de l\'envoi de l\'email de vérification. Veuillez réessayer plus tard.',
            });
        });


    }

    const [imageFile, setImageFile] = useState(null);

    const [userAvatar, setUserAvatar] = useState(null);

    useEffect(() => {
        if (personalInfoForm) {
            let imgPath = personalInfoForm.avatar_image;
            if(imgPath != null && imgPath!='') {
                let urlImage = domain + imgPath;
                console.log('urlImage:', urlImage);
                setUserAvatar(urlImage as any);
            }else {
                setUserAvatar(null);
            }
        }
    }, [personalInfoForm]);



    function uploadImage() {
        console.log('imageFile:', imageFile);
        let id = personalInfoForm.id;

        if (!imageFile) {
            return;
        }

        uploadAvatar(id , imageFile).then((res) => {
            Modal.success({
                title: 'Photo de profil téléchargée',
                content: 'L\'image a été téléchargée avec succès.',
            });
        }).catch((err) => {
            Modal.error({
                title: 'Erreur',
                content: 'Une erreur est survenue lors du téléchargement de l\'image. Veuillez réessayer plus tard.',
            });
        });
    }

    useEffect(() => {
        uploadImage();
    }, [imageFile]);

    const onAvatarChange = (file:any) => {
        setImageFile(file);
    }

    return (
        <div key={rerender} >

            <div  className={`mt-4 w-100 ${styles.templatesPersoDiv}`}>
                <h2 className={`display-6 mt-5`}>
                    Informations personnelles
                </h2>

                <Row
                   className={`w-100`}
                >
                    <>
                        <Form
                            name="userInfo"
                            onFinish={onFinish}
                            layout="vertical"
                            key={personalInfoForm.id}
                            className={`w-100`}
                        >


                            <strong className={`my-5 d-flex justify-content-start`}>
                                Informations d'indentification
                            </strong>

                            <Row gutter={16}>
                                <Col span={12}>
                                    <Form.Item initialValue={personalInfoForm.email} label="Identifiant E-mail" name="id" required>
                                        <Input
                                            readOnly={true}
                                            placeholder="Entrez votre adresse e-mail"
                                        />
                                    </Form.Item>
                                </Col>
                                <Col span={12} className={`w-100 d-flex align-items-center justify-content-end  profileTags `} >
                                    {personalInfoForm.role && (
                                        <>
                                            {personalInfoForm.role === 'ROLE_ADMIN' && (
                                                <Tag color="blue">Administrateur TipTop</Tag>
                                            )}

                                            {personalInfoForm.role === 'ROLE_STOREMANAGER' && (
                                                <Tag color="cyan">Manager de magasin</Tag>
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

                                       {personalInfoForm.is_activated && (
                                                <Tag color="green">
                                                    Compte Vérifié
                                                </Tag>
                                            )}

                                            {!personalInfoForm.is_activated && (
                                                <Tag color="red">
                                                    Compte non vérifié
                                                    </Tag>
                                            )}


                                    {!personalInfoForm.is_activated && (
                                        <Button className={` ${styles.cancelFormEmailTemplateBtn}`} type="default"
                                                onClick={() => {
                                                    sendActivationAccountEmail();
                                                }}
                                        >
                                            Vérifier le compte <SendOutlined />
                                        </Button>
                                    )}
                                </Col>
                            </Row>

                            <Row gutter={16}>
                                <Col span={12}>
                                    <Form.Item  label="Mot de passe" name="password">
                                        <Input
                                            readOnly={true}
                                            placeholder="************"
                                        />
                                    </Form.Item>
                                </Col>

                            </Row>



                            <Divider />


                            <strong className={`my-5 d-flex justify-content-start`}>
                                Informations de contact
                            </strong>

                            <Row gutter={[16, 16]}>
                                <Col span={12}>
                                    <Form.Item initialValue={personalInfoForm.lastname} label="Nom" name="lastname" required>
                                        <Input
                                            onChange={(e) => {
                                                setPersonalInfoForm({...personalInfoForm, lastname: e.target.value});
                                            }}
                                            placeholder="Entrez votre nom"
                                        />
                                    </Form.Item>
                                </Col>
                                <Col span={12}>
                                    <Form.Item initialValue={personalInfoForm.firstname} label="Prénom" name="firstname" required>
                                        <Input
                                            onChange={(e) => {
                                                setPersonalInfoForm({...personalInfoForm, firstname: e.target.value});
                                            }}
                                            placeholder="Entrez votre prénom"
                                        />
                                    </Form.Item>
                                </Col>
                            </Row>


                            <Row gutter={16}>

                                <Col span={12} className={``}>
                                    <Form.Item className={`w-100`} initialValue={personalInfoForm.phone} required label="Numéro de téléphone" name="phone">
                                        <Input
                                            onChange={(e) => {
                                                setPersonalInfoForm({...personalInfoForm, phone: e.target.value});
                                            }}
                                            placeholder="Entrez votre N° Tel " />
                                    </Form.Item>
                                </Col>
                            </Row>
                            <Row gutter={16}>
                                <Col span={12}>
                                    <Form.Item initialValue={personalInfoForm.address} label="Adresse" name="address" required>
                                        <Input
                                            onChange={(e) => {
                                                setPersonalInfoForm({...personalInfoForm, address: e.target.value});
                                            }}
                                            placeholder="Entrez votre adresse"
                                        />
                                    </Form.Item>
                                </Col>
                                <Col span={12}>
                                    <Form.Item initialValue={personalInfoForm.city} label="Ville" name="city" required>
                                        <Input
                                            onChange={(e) => {
                                                setPersonalInfoForm({...personalInfoForm, city: e.target.value});
                                            }}
                                            placeholder="Entrez votre ville"
                                        />
                                    </Form.Item>
                                </Col>
                            </Row>

                            <Row gutter={16}>
                                <Col span={12}>
                                    <Form.Item initialValue={personalInfoForm.postalCode} label="Code postal" name="postalCode" required>
                                        <Input
                                            onChange={(e) => {
                                                setPersonalInfoForm({...personalInfoForm, postalCode: e.target.value});
                                            }}
                                            placeholder="Entrez votre code postal"
                                        />
                                    </Form.Item>
                                </Col>
                                <Col span={12}>
                                    <Form.Item initialValue={personalInfoForm.country} label="Pays" name="country" required>
                                        <Input
                                            onChange={(e) => {
                                                setPersonalInfoForm({...personalInfoForm, country: e.target.value});
                                            }}
                                            placeholder="Entrez votre pays"
                                        />
                                    </Form.Item>
                                </Col>
                            </Row>


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



                            <Divider />


                            <strong className={`my-5 d-flex justify-content-start`}>
                                Photo de profil
                            </strong>

                            <Row gutter={16}>
                                <Col span={24} className={`d-flex align-items-center justify-content-center w-100`} >
                                    <AvatarUploader avatar={userAvatar} onImageChange={onAvatarChange}></AvatarUploader>
                                </Col>

                            </Row>


                            { personalInfoForm.role != 'ROLE_ADMIN' && (
                                <>
                                    <Divider />

                                    <strong className={`my-5 d-flex justify-content-start`}>
                                        Informations du magasin principal
                                    </strong>


                                    <Row gutter={16}>
                                        <Col span={12}>
                                            <Form.Item initialValue={personalInfoForm.store?.name ?? ''} label="Nom du magasin" name="storeName" >
                                                <Input
                                                    readOnly={true}
                                                    onChange={(e) => {
                                                        setPersonalInfoForm({...personalInfoForm, store: {...personalInfoForm.store, name: e.target.value}});
                                                    }}
                                                    placeholder="Aucun magasin n'est associé à votre compte"
                                                />
                                            </Form.Item>
                                        </Col>
                                        <Col span={12}>
                                            <Form.Item initialValue={personalInfoForm.store?.address ?? ''} label="Adresse du magasin" name="storeAddress" >
                                                <Input
                                                    readOnly={true}
                                                    onChange={(e) => {
                                                        setPersonalInfoForm({...personalInfoForm, store: {...personalInfoForm.store, address: e.target.value}});
                                                    }}
                                                    placeholder="Aucun magasin n'est associé à votre compte"
                                                />
                                            </Form.Item>
                                        </Col>
                                    </Row>

                                    <Row gutter={16}>
                                        <Col span={12}>
                                            <Form.Item initialValue={personalInfoForm.store?.city ?? ''} label="Ville du magasin" name="storeCity" >
                                                <Input
                                                    readOnly={true}
                                                    onChange={(e) => {
                                                        setPersonalInfoForm({...personalInfoForm, store: {...personalInfoForm.store, city: e.target.value}});
                                                    }}
                                                    placeholder="Aucun magasin n'est associé à votre compte"
                                                />
                                            </Form.Item>
                                        </Col>
                                        <Col span={12}>
                                            <Form.Item initialValue={personalInfoForm.store?.postal_code ?? ''} label="Code postal du magasin" name="storePostalCode">
                                                <Input
                                                    readOnly={true}
                                                    onChange={(e) => {
                                                        setPersonalInfoForm({...personalInfoForm, store: {...personalInfoForm.store, postal_code: e.target.value}});
                                                    }}
                                                    placeholder="Aucun magasin n'est associé à votre compte"
                                                />
                                            </Form.Item>
                                        </Col>
                                    </Row>


                                    <Row gutter={16}>
                                        <Col span={12}>
                                            <Form.Item initialValue={personalInfoForm.store?.country ?? ''} label="Pays du magasin" name="storeCountry" >
                                                <Input
                                                    onChange={(e) => {
                                                        setPersonalInfoForm({...personalInfoForm, store: {...personalInfoForm.store, country: e.target.value}});
                                                    }}
                                                    readOnly={true}
                                                    placeholder="Aucun magasin n'est associé à votre compte"
                                                />
                                            </Form.Item>
                                        </Col>
                                        <Col span={12}>
                                            <Form.Item initialValue={personalInfoForm.store?.phone ?? ''} label="Numéro de téléphone du magasin" name="storePhone">
                                                <Input
                                                    onChange={(e) => {
                                                        setPersonalInfoForm({...personalInfoForm, store: {...personalInfoForm.store, phone: e.target.value}});
                                                    }}
                                                    readOnly={true}
                                                    placeholder="Aucun magasin n'est associé à votre compte"
                                                />
                                            </Form.Item>
                                        </Col>
                                    </Row>

                                    <Row gutter={16}>
                                        <Col span={12}>
                                            <Form.Item initialValue={personalInfoForm.store?.email ?? ''} label="Adresse E-mail du magasin" name="storeEmail">
                                                <Input
                                                    onChange={(e) => {
                                                        setPersonalInfoForm({...personalInfoForm, store: {...personalInfoForm.store, email: e.target.value}});
                                                    }}
                                                    readOnly={true}
                                                    placeholder="Aucun magasin n'est associé à votre compte"
                                                />
                                            </Form.Item>
                                        </Col>

                                    </Row>


                                </>
                            )}






                        </Form>
                    </>
                </Row>

            </div>

        </div>
    );
}

export default PersonalInformations;