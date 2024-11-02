import React, {useEffect,useState} from 'react';
import {Button, Col, message, Modal, Row, Space, Table, Tag} from 'antd';
import type { ColumnsType } from 'antd/es/table';
import {
    DeleteColumnOutlined,
    DeleteOutlined,
    DownloadOutlined,
    EditOutlined,
    ExclamationCircleFilled,
    StopOutlined
} from "@ant-design/icons";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {deleteStoreById, getStoreById} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import ModalAddOrUpdateStore
    from "@/app/components/dashboardComponents/StoreManagementComponent/components/ModalAddOrUpdateStore";

import Styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

interface DataType {
    key: string;
    name: string;
    address: string;
    status: number|string;
    managerUserCount: number;
    employeeUserCount: number;
    clientUserCount: number;
    playedTickets: number;
    siren: string;
}


interface storeCardsProps {
    selectedStoreId: string;
    onStoreUpdate: () => void;
    isStoresUpdated: boolean;
    changeSelectedStore: (value: string) => void;
}

function StoreDataInfoTable({ selectedStoreId , onStoreUpdate, isStoresUpdated , changeSelectedStore }: storeCardsProps) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [updateFormKey, setUpdateFormKey] = useState(0);

    const [userRole , setUserRole] = useState<string | null>(null);
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
    }, []);


    const { confirm } = Modal;
    const showStoreDeleteModal = () => {
        showConfirm();
    }
    const showConfirm = () => {
        confirm({
            title: 'Voulez-vous vraiment supprimer ce magasin ?',
            icon: <ExclamationCircleFilled />,
            content: <>Impossible de supprimer un magasin s'il contient des utilisateurs associés.</>,
            okText : "D'accord",
            onOk() {
                deleteStoreById(selectedStoreId).then((response) => {
                    onStoreUpdate();
                    selectedStoreId = '';
                    changeSelectedStore(selectedStoreId);

                    console.log("selectedStoreId : " , selectedStoreId);
                    message.success('Magasin supprimé avec succès');
                }).catch((err : any) => {
                    if (err.response) {
                        if (err.response.status === 401) {
                            logoutAndRedirectAdminsUserToLoginPage();
                        } else if (err.response.status === 400) {
                            message.error(
                                <>
                                    Un problème est survenu lors de la suppression du magasin. <br/>
                                    <strong> Magasin contient des utilisateurs. </strong>
                                </>
                            );
                        }
                    } else {
                        message.error(
                            <>
                                Un problème est survenu lors de la suppression du magasin. <br/>
                                <strong>Veuillez réessayer ultérieurement.</strong>
                            </>
                        );
                    }
                })

            },
            cancelText : "Annuler",
            onCancel() {
                console.log('Cancel');
            },
        });
    };


    const showStoreUpdateModal = () => {

        setIsModalOpen(true);
        setUpdateFormKey(updateFormKey + 1);
    };

    const closeStoreUpdateModal = () => {
        setIsModalOpen(false);
    };

    const { logoutAndRedirectAdminsUserToLoginPage } = LogoutService();
    const columns: ColumnsType<DataType> = [
        {
            title: 'Nom du magasin',
            dataIndex: 'name',
            key: 'name',
            render: (text) => <a>{text}</a>,
        },
        {
            title: 'Siren',
            dataIndex: 'siren',
            key: 'siren',
            render: (text) => <a>{text}</a>,
        },
        {
            title: 'Adresse',
            dataIndex: 'address',
            key: 'address',
        },
        {
            title: 'Nbr de managers',
            dataIndex: 'managerUserCount',
            key: 'managerUserCount',
        },
        {
            title: 'Nbr d\'employés',
            dataIndex: 'employeeUserCount',
            key: 'employeeUserCount',
        },
        {
            title: 'Nbr de clients inscrits',
            dataIndex: 'clientUserCount',
            key: 'clientUserCount',
        },
        {
            title: 'Nbr de tickets joués',
            dataIndex: 'playedTickets',
            key: 'playedTickets',
        },
        {
            title: 'Status',
            key: 'tags',
            dataIndex: 'tags',
            render: (_, { status }) => (
                <>
                    {status==1 && (
                        <Tag color={'green'} key={status}>
                            Ouvert
                        </Tag>
                    ) || status==2 && (
                        <Tag color={'red'} key={status}>
                            Fermé
                        </Tag>
                    )}
                </>
            ),
        },
        {
            title: 'Action',
            key: 'action',
            render: (_, record) => (
               <>
                   <Space size="middle">
                       <Button onClick={showStoreUpdateModal} className={`${styles.updateStoreArrayBtn}`} icon={<EditOutlined />} size={"middle"}>
                           Modifier
                       </Button>
                       {userRole === 'ROLE_ADMIN' && (<>
                       <Button onClick={showStoreDeleteModal} className={`${styles.deleteStoreArrayBtn}`} icon={<DeleteOutlined />} size={"middle"}>
                       </Button>
                       </>)}
                   </Space>

               </>


            ),
        },
    ];


    const [storeData, setStoreData] = useState<DataType[]>([]);
    function getStoreByIdFunction(selectedStoreId: string) {
        getStoreById(selectedStoreId).then((response) => {
            const newData: DataType[] = [
                {
                    key: selectedStoreId,
                    name: response.storeResponse.name,
                    address: response.storeResponse.address + " " + response.storeResponse.city + " " + response.storeResponse.postal_code + " , " + response.storeResponse.country,
                    status: response.storeResponse.status,
                    managerUserCount: response.managerUserCount,
                    employeeUserCount: response.employeeUserCount,
                    clientUserCount: response.clientUserCount,
                    playedTickets: response.playedTickets,
                    siren: response.storeResponse.siren,
                },
            ];
            setStoreData(newData);
        }).catch((err) => {
            setStoreData([]);
            if (err.response){
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
                if (err.response.status === 400 ) {
                    onStoreUpdate();
                }
                if (err.response.status === 404 ) {
                    window.location.reload();
                }

            }else {
                console.log(err.request);
            }
        });

    }

    useEffect(() => {
        if (selectedStoreId) {
            getStoreByIdFunction(selectedStoreId);
        }
    }, [selectedStoreId,isStoresUpdated]);



    const customEmptyText = (
        <div className={styles.emptyTableTextDiv}>
            <span>Aucun magasin séléctionné</span>
            <span><StopOutlined /></span>
        </div>
    );
    return (
        <div className={`${Styles.mainTable}`}>
            <Table  locale={{emptyText : customEmptyText}} pagination={false} columns={columns} dataSource={storeData} />
            {selectedStoreId && (
                <ModalAddOrUpdateStore onStoreUpdate={onStoreUpdate} key={updateFormKey} storeId={selectedStoreId}  modalIsOpen={isModalOpen} closeModal={closeStoreUpdateModal}  updateStore={true} ></ModalAddOrUpdateStore>
            )}
        </div>
    );
}

export default StoreDataInfoTable;