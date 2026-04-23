import * as React from 'react';
import Box from '@mui/material/Box';
import SpeedDial from '@mui/material/SpeedDial';
import SpeedDialIcon from '@mui/material/SpeedDialIcon';
import SpeedDialAction from '@mui/material/SpeedDialAction';
import { FaFacebookMessenger } from 'react-icons/fa';
import WhatsAppIcon from '@mui/icons-material/WhatsApp';
import LocalPhoneIcon from '@mui/icons-material/LocalPhone';
import { Link } from 'react-router-dom';
import { useGeneralDataQuery } from '../../redux/features/api';

const ChatSpeedDial = () => {
  const { data } = useGeneralDataQuery();
  const actions = [
    {
      icon: <LocalPhoneIcon />,
      name: 'phone',
      href: `tel: ${data?.generalData?.phone}`,
    },
    {
      icon: <WhatsAppIcon color="success" />,
      name: 'Whatsapp',
      href: `https://wa.me/${data?.generalData?.whatsapp}`,
    },
    {
      icon: <FaFacebookMessenger className="w-6 h-6 text-blue-500" />,
      name: 'Messenger',
      href: `https://m.me/${data?.generalData?.messenger}`,
    },
  ];
  return (
    <div>
      <Box sx={{ height: 320, transform: 'translateZ(0px)', flexGrow: 1 }}>
        <SpeedDial
          ariaLabel="SpeedDial basic example"
          sx={{ position: 'absolute', bottom: 16, right: 16 }}
          icon={<SpeedDialIcon color="primary" />}
        >
          {actions.map((action) => (
            <SpeedDialAction
              key={action.name}
              tooltipTitle={action.name}
              icon={
                <Link to={action.href} style={{ color: 'inherit' }}>
                  {action.icon}
                </Link>
              }
            />
          ))}
        </SpeedDial>
      </Box>
    </div>
  );
};

export default ChatSpeedDial;
