import React, {useEffect, useState} from 'react';
import {Button, message, Modal, Spin} from 'antd';

interface ModalProps {
    modalIsOpen: boolean;
    closeModal: () => void;
    updateStore: boolean;
    storeId: string | null;
    onStoreUpdate: () => void;
    changeSelectedStore?: (value: string) => void;
}

import {
    DatePicker,
    Form,
    Input,
    InputNumber,
    Switch,
} from 'antd';

import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";


type storeForm = {
    name: string;
    address: string;
    postal_code: string;
    city: string;
    country: string;
    email: string;
    opening_date: string;
    status: string;
    capital: string;
    headquarters_address: string;
    phone_number: string;
    siren: string;
}
const storeFormDataDefault = {
    name: '',
    address: '',
    postal_code: '',
    city: '',
    country: '',
    email: '',
    opening_date: '',
    status: '1',
    capital: '',
    headquarters_address: '',
    phone_number: '',
    siren: '',

};
const dateFormat = 'DD/MM/YYYY';

import type {DatePickerProps} from 'antd';
import 'dayjs/locale/fr';
import locale from 'antd/locale/fr_FR';
import {ConfigProvider} from 'antd';
import {EuroCircleOutlined} from "@ant-design/icons";
import {addNewStoreByAdmin, getStoreById , updateStoreByAdminWithId} from "@/app/api/endpoints/storesApi";
import dayjs from "dayjs";
import moment from "moment";

function ModalAddOrUpdateStore({modalIsOpen, closeModal, updateStore, storeId,onStoreUpdate , changeSelectedStore}: ModalProps) {
    const [storeFormData, setStoreFrom] = useState<storeForm>(storeFormDataDefault);
    const [formRef] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [modalKey, setModalKey] = useState(0);
    const handleOk = async () => {
        try {
            setLoading(true);
            message.destroy();
            await formRef.validateFields();

            if (updateStore) {
                if (storeId == null) {
                    console.log("ID is null");
                    return;
                }
                await updateStoreByAdminWithId(storeId , storeFormData).then((response) => {
                    console.log(response);
                    message.success('Magasin a été modifié avec succés');
                    closeModal();
                    onStoreUpdate();
                    setStoreFrom(
                        {
                            name: '',
                            address: '',
                            postal_code: '',
                            city: '',
                            country: '',
                            email: '',
                            opening_date: '',
                            status: '1',
                            capital: '',
                            headquarters_address: '',
                            phone_number: '',
                            siren: '',
                        }
                    );
                    formRef.resetFields();
                }).catch((err) => {
                    console.log(err);
                    message.error('Un problème est survenu lors de la modification du magasin');
                });
            } else {
                await addNewStoreByAdmin(storeFormData).then((response) => {
                    console.log(response);
                    message.success('Magasin a été ajouté avec succés');
                    if (changeSelectedStore) {
                        changeSelectedStore(response.storeResponse.id);
                    }
                    closeModal();
                    onStoreUpdate();
                    setStoreFrom(
                        {
                            name: '',
                            address: '',
                            postal_code: '',
                            city: '',
                            country: '',
                            email: '',
                            opening_date: '',
                            status: '1',
                            capital: '',
                            headquarters_address: '',
                            phone_number: '',
                            siren: '',
                        }
                    );
                    formRef.resetFields();
                }).catch((err) => {
                    console.log(err);
                    message.error('Un problème est survenu lors de l\'ajout du magasin');

                });
            }

        } catch (errorInfo) {
            setLoading(false);
            message.error('Veuillez vérifier les informations saisies');
        } finally {
            setLoading(false);
        }
    };

    const handleCancel = () => {
        closeModal();
    };


    const handleDateChange: DatePickerProps['onChange'] = (date, dateString) => {
        if (dateString && date) {
            let ch = date.format('DD/MM/YYYY');
            setStoreFrom((prevFormData) => ({
                ...prevFormData,
                opening_date: ch,
            }));
        }
    };

    const storeFormHandleChange = (e: React.ChangeEvent<HTMLInputElement>, ch: string) => {
        let inputValue = e.target.value;
        const currencyPattern = /^[0-9]*\.?[0-9]{0,2}$/;
        const numbersOnly = /^[0-9]*$/;

        if (ch === "capital") {
            if (!currencyPattern.test(inputValue)) {
                inputValue = inputValue.replace(/[^0-9.]/g, "");
                e.preventDefault();
            }
        } else if (ch === "siren" || ch === "postal_code") {
            if (!numbersOnly.test(inputValue)) {
                inputValue = inputValue.replace(/[^0-9]/g, "");
                e.preventDefault();
            }
        }

        setStoreFrom((prevFormData) => ({
            ...prevFormData,
            [ch]: inputValue,
        }));
    };


    const storeFormHandleChangeStatus = (e: boolean) => {
        setStoreFrom((prevFormData) => ({
            ...prevFormData,
            status: e ? "1" : "2",
        }));
    }

    useEffect(() => {
        if (storeId && updateStore) {
            setModalKey(modalKey + 1);
            getStoreById(storeId).then((response) => {
                console.log(response);
                setStoreFrom((prevFormData) => ({
                    ...prevFormData,
                    name: response.storeResponse.name,
                    address: response.storeResponse.address,
                    postal_code: response.storeResponse.postal_code,
                    city: response.storeResponse.city,
                    country: response.storeResponse.country,
                    email: response.storeResponse.email,
                    opening_date: response.storeResponse.opening_date,
                    status: response.storeResponse.status,
                    capital: response.storeResponse.capital,
                    headquarters_address: response.storeResponse.headquarters_address,
                    phone_number: response.storeResponse.phone,
                    siren: response.storeResponse.siren,
                }));

                formRef.setFieldsValue({
                    name: response.storeResponse.name,
                    address: response.storeResponse.address,
                    postal_code: response.storeResponse.postal_code,
                    city: response.storeResponse.city,
                    country: response.storeResponse.country,
                    email: response.storeResponse.email,
                    opening_date: dayjs(response.storeResponse.opening_date, dateFormat),
                    status: response.storeResponse.status == "1" ? 1 : 2,
                    capital: response.storeResponse.capital,
                    headquarters_address: response.storeResponse.headquarters_address,
                    phone_number: response.storeResponse.phone,
                    siren: response.storeResponse.siren,
                });
            }).catch((err: any) => {
                console.log(err);
            });
        }

    }, [storeId, updateStore]);


    return (
        <div className={`${styles.storeAddUpdateModalDiv} storeAddUpdateModalDiv`}>
            <Modal key={modalKey} title={updateStore ? 'Modification' : 'Ajouter un nouveau magasin'} open={modalIsOpen}
                   onOk={handleOk} onCancel={handleCancel}
                   style={{minWidth: 730}}
                   okText={updateStore ? "Modifier" : "Ajouter"}
                   cancelText={"Annuler"}
                   className={`${styles.storeAddUpdateModal} storeAddUpdateModal`}

                   footer={<>
                       <Button
                           onClick={handleCancel}
                       >
                           Annuler
                       </Button>
                       <Button
                           type="primary"
                           htmlType="submit"
                           loading={loading}
                           disabled={loading}
                           onClick={handleOk}
                       >
                           {updateStore ? 'Modifier' : 'Ajouter'}
                       </Button>
                   </>}

            >
                <Form

                    labelCol={{span: 6}}
                    wrapperCol={{span: 16}}
                    layout="horizontal"
                    form={formRef}
                >

                    <Form.Item
                        rules={[{required: true, message: "Veuillez saisir un nom du boutique "}]}
                        label="Nom du magasin"
                        name={"name"}
                    >
                        <Input value={storeFormData.name} onChange={(e) => {
                            storeFormHandleChange(e, "name");
                        }} placeholder={"Nom"}/>
                    </Form.Item>
                    <Form.Item
                        rules={[{required: true, message: "Veuillez saisir le Siren du boutique"}]}
                        label="Numéro Siren"
                        name={"siren"}
                    >
                        <Input onKeyDown={(event) => {
                            const re = /^[0-9\b]+$/;
                            if ((!re.test(event.key) && event.key !== 'Backspace')) {
                                event.preventDefault();
                            }
                            if (event.currentTarget.value.length > 9 && event.key !== 'Backspace') {
                                event.preventDefault();
                            }
                        }} value={storeFormData.siren} onChange={(e) => {
                            storeFormHandleChange(e, "siren");
                        }} placeholder={"Siren"}/>
                    </Form.Item>
                    <Form.Item
                        name={"address"}
                        rules={[{required: true, message: "Veuillez saisir une adresse"}]}
                        label="Adresse">
                        <Input value={storeFormData.address} onChange={(e) => {
                            storeFormHandleChange(e, "address");
                        }} placeholder="N° , rue ..."/>
                    </Form.Item>
                    <Form.Item required={true} label="CP , Ville : " style={{marginBottom: 0}}>
                        <Form.Item
                            name={"postal_code"}
                            rules={[{required: true, message: "CP Obligatorie"}]}
                            style={{display: 'inline-block', width: 'calc(30% - 8px)'}}
                        >
                            <Input value={storeFormData.postal_code} onChange={(e) => {
                                storeFormHandleChange(e, "postal_code");
                            }} placeholder="CP"
                                   onKeyDown={(event) => {
                                       const re = /^[0-9\b]+$/;
                                       if ((!re.test(event.key) && event.key !== 'Backspace')) {
                                           event.preventDefault();
                                       }
                                       if (event.currentTarget.value.length > 6 && event.key !== 'Backspace') {
                                           event.preventDefault();
                                       }
                                   }}
                            />
                        </Form.Item>
                        <Form.Item
                            name={"city"}
                            rules={[{required: true, message: "Veuillez saisir une ville"}]}
                            style={{display: 'inline-block', width: 'calc(40% - 8px)'}}
                        >
                            <Input value={storeFormData.city} placeholder="Ville"
                                   onChange={(e) => {
                                       storeFormHandleChange(e, "city");
                                   }}
                            />
                        </Form.Item>
                    </Form.Item>

                    <Form.Item
                        rules={[{required: true, message: "Veuillez saisir un pays"}]}
                        name={"country"}
                        label="Pays">

                        <Input value={storeFormData.country} placeholder={"France"} onChange={(e) => {
                            storeFormHandleChange(e, "country");
                        }}/>
                    </Form.Item>
                    <Form.Item
                        rules={[{required: true, message: "Veuillez saisir un e-mail"}]}
                        name={"email"}
                        label="E-mail">
                        <Input value={storeFormData.email} placeholder={"exemple@exemple.com"} onChange={(e) => {
                            storeFormHandleChange(e, "email");
                        }}/>
                    </Form.Item>
                    <Form.Item
                        rules={[{required: true, message: "Veuillez saisir un numéro de téléphone "}]}
                        name={"phone_number"}
                        label="Numéro de téléphone ">
                        <Input
                            onKeyDown={(event) => {
                                const re = /^[0-9\b+]+$/;
                                if ((!re.test(event.key) && event.key !== 'Backspace')) {
                                    event.preventDefault();
                                }
                                if (event.currentTarget.value.length > 13 && event.key !== 'Backspace') {
                                    event.preventDefault();
                                }
                            }}
                            value={storeFormData.phone_number} placeholder={"+33 .. .. .. .. .."} onChange={(e) => {
                            storeFormHandleChange(e, "phone_number");
                        }}/>
                    </Form.Item>
                    <Form.Item
                        name={"opening_date"}
                        rules={[{required: false}]}
                        label="Date d'ouverture">
                        <ConfigProvider locale={locale}>
                            <DatePicker
                                name={"opening_date"}
                                onChange={handleDateChange}
                                format={dateFormat} placeholder={"Sélectionner une date"}
                                value={storeFormData.opening_date ? dayjs(storeFormData.opening_date, dateFormat) : null}

                            />
                        </ConfigProvider>
                    </Form.Item>
                    <Form.Item label="Status" name={"status"}>
                        <Switch checked={storeFormData.status == "1"} onChange={(e: boolean) => {
                            storeFormHandleChangeStatus(e);
                        }}/>
                    </Form.Item>
                    <Form.Item
                        name={"capital"}
                        rules={[{required: true, message: "Veuillez saisir un capital"}]}
                        label="Capital">
                        <Input value={storeFormData.capital} type={"number"} step={"0.01"} onKeyDown={(event) => {
                            const re = /^[0-9]*(\.[0-9]{0,2})?$/;
                            if ((!re.test(event.key) && event.key !== 'Backspace')) {
                                event.preventDefault();
                            }
                        }}
                               min={0}
                               suffix={<EuroCircleOutlined/>} onChange={(e) => {
                            storeFormHandleChange(e, "capital");
                        }} placeholder={"Capital"}/>
                    </Form.Item>
                    <Form.Item
                        name={"headquarters_address"}
                        rules={[{required: true, message: "Veuillez saisir un siége social"}]}
                        label="Siège social : ">
                        <Input value={storeFormData.headquarters_address} onChange={(e) => {
                            storeFormHandleChange(e, "headquarters_address");
                        }} placeholder="N° , rue ..."/>
                    </Form.Item>


                </Form>
            </Modal>
        </div>
    );
}

export default ModalAddOrUpdateStore;