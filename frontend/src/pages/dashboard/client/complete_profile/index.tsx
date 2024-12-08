import React, {useEffect, useState} from 'react'
import {Col, Row} from "react-bootstrap";
import '../../../../app/globals.css'

import styles from '../../../../styles/pages/auth/clientRegisterPage.module.css'
import {Button, ConfigProvider, DatePicker, type DatePickerProps, Form, Input, Modal, Select, Space} from "antd";
import {
    PhoneOutlined,
    UserAddOutlined,
    UserOutlined
} from "@ant-design/icons";
import Image from "next/image";
import logoTipTopImg from "@/assets/images/logovf.jpeg";
import locale from "antd/locale/fr_FR";
import {saveUserProfile} from "@/app/api";

type registerUserForm = {
    firstname?: string;
    lastname?: string;
    gender?: string;
    phone?: string;
    dateOfBirth?: string;
}

const userFormData = {
    firstname: '',
    lastname: '',
    dateOfBirth: "",
    phone: '',
    gender: "",
};


const dateFormat = 'DD/MM/YYYY';


export default function index() {


    const [userForm, setUserForm] = useState(userFormData);

    const [loadingButton, setLoadingButton] = useState(false);


    const validateMessages = {
        required: 'Ce champ est obligatoire !',
    };

    const onFinish = (values: any) => {
        console.log('Success:', values);
    };

    const onFinishFailed = (errorInfo: any) => {
        console.log('Failed:', errorInfo);
    };

    const userGenderFormHandleChange = (value: any) => {
        setUserForm((prevFormData : any) => ({
            ...prevFormData,
            gender: value,
        }));
    }



    const handleDateChange: DatePickerProps['onChange'] = (date, dateString) => {
        console.log(date, dateString);
        if (dateString && date) {
            console.log(date.format('DD/MM/YYYY'));
            let ch = date.format('DD/MM/YYYY');
            setUserForm((prevFormData : any) => ({
                ...prevFormData,
                dateOfBirth: ch,
            }));
        }
    };



    useEffect(() => {
        let user = localStorage.getItem('loggedInUser');
        if (user) {
            let userObj = JSON.parse(user);
            console.log(userObj);
            setUserForm((prevFormData : any) => ({
                ...prevFormData,
                firstname: userObj.firstname,
                lastname: userObj.lastname,
                email: userObj.email,
                phone: userObj.phone,
            }));
            let userStore = userObj.store;
            if (userStore) {
                //window.location.href = '/dashboard/client';
            }

        }


    }, []);


    useEffect(() => {
        console.log(userForm);
    }, [userForm]);

    const userFormHandleChange = (e: React.ChangeEvent<HTMLInputElement>, ch: string) => {
        let inputValue = e.target.value;
        setUserForm((prevFormData : any) => ({
            ...prevFormData,
            [ch]: inputValue,
        }));
    }


    function updateUserProfile() {
        setLoadingButton(true);
        if (userForm.firstname == "" || userForm.lastname==""  || userForm.dateOfBirth==""
            || userForm.phone=="" || userForm.gender==""
        ) {
            Modal.error({
                className: 'antdLoginRegisterModal',
                title: 'Un problème est survenu !',
                content: <>
                    <span>Veuillez remplir tous les champs.</span> <br/>
                </>,
                okText: "D'accord",
            });
        }


        if (userForm.dateOfBirth == "") {
            console.log("dateOfBirth is empty");
            setLoadingButton(false)
            return;
        }
        if (userForm.phone == "") {
            console.log("phone is empty");
            setLoadingButton(false)
            return;
        }


        if(userForm.gender == ""){
            console.log("gender is empty");
            setLoadingButton(false)
            return;
        }


        saveUserProfile(userForm).then((response) => {
            setLoadingButton(false);
                Modal.success({
                    className: 'modalSuccess antdLoginRegisterModal',
                    title : 'Profil mis à jour !',
                    content: <>
                        <strong>Votre profil a été mis à jour avec succès.</strong> <br/>
                    </>,
                    okText: "D'accord",
                });
                localStorage.setItem('firstLoginClientStatus', 'true');
            window.location.href = '/dashboard/client/favorite_store_selection';
        }).catch((err) => {
            setLoadingButton(false);
            console.log(err);
            if (err.response) {
                if (err.response.status === 400) {
                    console.log(err.response.data.error);
                }else{
                    Modal.error({
                        className: 'antdLoginRegisterModal',
                        title: 'Un problème est survenu !',
                        content: <>
                            <span>Un problème est survenu lors de la mise à jour du profil.</span> <br/>
                            <span>Veuillez réessayer plus tard.</span>
                        </>,
                        okText: "D'accord",
                    });
                }
            } else {
                console.log(err.request);
            }
        });

    }

    return (
        <div className={`${styles.loginForm} `} style={{
            display: "flex",
            flexDirection: "column",
            justifyContent: "center",
            alignItems: "center",
            padding: "0",
        }}
        key={userForm.firstname}
        >
            <Row className={`${styles.loginFormTopHeader} p-0 m-0`} style={{paddingTop : "0 !important"}}>

                <Col>
                    <h1>
                        Completez votre profil
                        <UserAddOutlined className={`${styles.loginIcon}`}/></h1>
                </Col>


            </Row>
            <Row className={`${styles.loginLogo} p-0 m-0`}>
                <a className={`${styles.loginLogo} p-0 m-0`} href="/">
                    <Image
                        src={logoTipTopImg}
                        alt="Picture of the author"
                    >

                    </Image>
                </a>
            </Row>

            <Row className={`mt-5 px-md-5 px-sm-1  d-flex justify-content-center `}>
                <Form
                    name="basic"
                    labelCol={{span: 8}}
                    wrapperCol={{span: 24}}
                    initialValues={{remember: +true}}
                    onFinish={onFinish}
                    onFinishFailed={onFinishFailed}
                    autoComplete="off"
                    validateMessages={validateMessages}
                    className={`${styles.registerForm}`}
                >
                    <Space.Compact>
                        <Form.Item<registerUserForm>
                            name="lastname"
                            initialValue={userForm.lastname}
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                        >
                            <Input
                                onChange={(e) => {
                                userFormHandleChange(e, "lastname");
                            }}
                                   placeholder='Nom' className={`${styles.inputsLoginPage}`}
                                   prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                        </Form.Item>
                        <Form.Item<registerUserForm>
                            name="firstname"
                            initialValue={userForm.firstname}
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                        >
                            <Input onChange={(e) => {
                                userFormHandleChange(e, "firstname");
                            }} placeholder='Prénom' className={`${styles.inputsLoginPage}`}
                                   prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                        </Form.Item>
                    </Space.Compact>






                    <Space.Compact>
                        <Form.Item<registerUserForm>
                            name="phone"
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                        >
                            <Input
                                onChange={(e) => {
                                    userFormHandleChange(e, "phone");
                                }}
                                placeholder='Numéro de téléphone' className={`${styles.inputsLoginPage}`}
                                prefix={<PhoneOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                        </Form.Item>
                        <Form.Item<registerUserForm>
                            name="gender"
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                            validateStatus={userForm.gender=="" ? 'error' : ''}
                        >
                            <Select onChange={(value) => {
                                userGenderFormHandleChange(value);
                            }}

                                    placeholder="Sélectionnez votre genre" className={`${styles.inputsLoginPage}`}>
                                <Select.Option value="Homme">Homme</Select.Option>
                                <Select.Option value="Femme">Femme</Select.Option>
                                <Select.Option value="Autre">Autre</Select.Option>
                            </Select>
                        </Form.Item>
                    </Space.Compact>


                    <Space.Compact style={{
                        display: "flex",
                        flexDirection: "row",
                        justifyContent: "space-between",
                        alignItems: "center",
                        width: "100%"
                    }}>
                        <Form.Item<registerUserForm>
                            name="dateOfBirth"
                            rules={[{
                                required: userForm.dateOfBirth == "",
                                message: 'La date de naissance est requise.'
                            }]}
                            validateStatus={userForm.dateOfBirth == "" ? 'error' : ''}
                        >
                            <ConfigProvider locale={locale}>
                                <DatePicker
                                    onChange={handleDateChange}
                                    format={dateFormat}
                                    className={`${styles.inputsLoginPage}`} renderExtraFooter={() =>
                                    <>
                                        <span className='mx-4'>Date de naissance</span>
                                    </>
                                }
                                    placeholder='Date de naissance'
                                />
                            </ConfigProvider>
                        </Form.Item>

                        <Form.Item className={``}
                        style={{
                            maxWidth: "200px",
                        }}
                        >
                            <Button
                                style={{
                                    width: "100%",
                                    borderRadius: "6px",
                                }}
                                loading={loadingButton}
                                disabled={loadingButton}
                                onClick={() => {
                                    updateUserProfile();
                                }}
                                className={`${styles.loginBtnSubmit}`} type="primary" htmlType="submit">
                                Enregistrer
                            </Button>
                        </Form.Item>
                    </Space.Compact>



                </Form>

            </Row>


            <Row>
                <Col>
                    <div className={`${styles.navLinkLogin} d-flex`}>
                        <p>&copy; 2024 Furious Ducks. Tous droits réservés.</p>
                    </div>
                </Col>
            </Row>
        </div>
    )
}
