import {Avatar, Modal, Upload} from 'antd';
import {UploadOutlined, UserOutlined} from '@ant-design/icons';
import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

interface AvatarUploaderProps {
    onImageChange: (file: any) => void;
    avatar: string|null;
}
const AvatarUploader = ({onImageChange , avatar} : AvatarUploaderProps) => {
    const [avatarUrl, setAvatarUrl] = useState(null);

    useEffect(() => {
        //if (avatar) setAvatarUrl(avatar as any);
    }, []);

    const handleChange = (info: any) => {
       Modal.confirm({
            title: 'Êtes-vous sûr de vouloir changer votre photo de profil ?',
            content: 'Vous ne pourrez pas revenir en arrière',
            okText: 'Oui',
            cancelText: 'Non',
            onOk: () => {
                console.log(info);

                if (info.file) {
                    const allowedFormats = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!allowedFormats.includes(info.file.type)) {
                        console.error('Invalid image format. Please upload a JPEG, PNG, or GIF image.');
                        return;
                    }

                    const maxSizeInBytes = 5 * 1024 * 1024;
                    if (info.file.size > maxSizeInBytes) {
                        console.error('Image size exceeds the maximum allowed size (5 MB).');
                        Modal.error({
                            title: 'Erreur',
                            content: <>
                                <p>
                                    La taille de l\'image dépasse la taille maximale autorisée
                                </p>
                                <p>
                                    Taille maximale autorisée : 5 MB
                                </p>
                            </>,
                        });

                    }

                    const reader: any = new FileReader();
                    reader.addEventListener('load', () => {
                        onImageChange(info.file.originFileObj);
                        console.log('Reader result:', reader.result);
                        setAvatarUrl(reader.result);
                    });

                    const blob = new Blob([info.file.originFileObj], { type: info.file.type });
                    reader.readAsDataURL(blob);
                }
            },
       });


    };



    const customRequest = ({ onSuccess }:any) => {

    };

    return (
        <Upload
            customRequest={customRequest}
            showUploadList={false}
            onChange={handleChange}
            className={`w-100`}
        >
            <Avatar
                size={64}
                icon={!avatarUrl ? <UserOutlined className={`${styles.avatarUploaderIcon}`} /> : null}
                src={avatarUrl ? avatarUrl : avatar}
                alt="Avatar"
                className={`mx-auto d-flex justify-content-center align-items-center ${styles.avatarUploader}`}
            />
            <div className={`my-3 ${styles.avatarUploaderText}`} style={{ marginTop: 8 }}>
                <UploadOutlined />
                <span className={`mx-3`}>
                    {!avatarUrl ? 'Choisir une photo de profil' : "Changer la photo de profil"}

                </span>
            </div>
        </Upload>
    );
};

export default AvatarUploader;
