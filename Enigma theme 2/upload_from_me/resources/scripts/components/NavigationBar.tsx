import * as React from 'react';
import { Link, NavLink } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCogs, faLayerGroup, faSignOutAlt, faUserCircle } from '@fortawesome/free-solid-svg-icons';
import { useStoreState } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import SearchContainer from '@/components/dashboard/search/SearchContainer';
import tw, { theme } from 'twin.macro';
import styled from 'styled-components/macro';
import http from '@/api/http';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import { useState } from 'react';

const Navigation = styled.div`
    ${tw`w-full bg-neutral-900 shadow-md overflow-x-auto`};
    
    & > div {
        ${tw`mx-auto w-full flex items-center`};
    }
    
    & #logo {
        ${tw`flex-1`};
        
        & > a {
            ${tw`text-2xl font-header px-4 no-underline text-neutral-200 hover:text-neutral-100 transition-colors duration-150`};
        }
    }
`;

const RightNavigation = styled.div`
    ${tw`flex h-full items-center justify-center`};
    
    & > a, & > button, & > .navigation-link {
        ${tw`flex items-center h-full no-underline text-neutral-300 px-6 cursor-pointer transition-all duration-150`};
        
        &:active, &:hover {
            ${tw`text-neutral-100 bg-black`};
        }
        
        &:active, &:hover, &.active {
            box-shadow: inset 0 -2px ${theme`colors.cyan.700`.toString()};
        }
    }
`;

export default () => {
    const name = useStoreState((state: ApplicationStore) => state.settings.data!.name);
    const rootAdmin = useStoreState((state: ApplicationStore) => state.user.data!.rootAdmin);
    const [ isLoggingOut, setIsLoggingOut ] = useState(false);

    const onTriggerLogout = () => {
        setIsLoggingOut(true);
        http.post('/auth/logout').finally(() => {
            // @ts-ignore
            window.location = '/';
        });
    };

    return (
        <Navigation>
            <SpinnerOverlay visible={isLoggingOut} />
            <div className="header">
				<div className="logo"></div>
				<a id={'server_link'} className="header-link" href={'/'}>
					<i className="fas fa-server fa-lg"></i>
					My servers
				</a>
				<a id={'our_website'} className="header-link" href={'#'}>
					<i className="fas fa-external-link-alt fa-lg"></i>
					Our website
				</a>
				<a onClick={onTriggerLogout} className="header-link" href={'#'}>
					<i className="fas fa-sign-out-alt fa-lg"></i>
					Logout
				</a>
				<div className="user-info">
					<a href={'/account'}><button className="button">
						<i className="fas fa-user"></i>
						My profile
					</button></a>
					{rootAdmin &&
					<a href={'/admin'}><button className="button">
						<i className="fas fa-user"></i>
						Admin panel
					</button></a>
					}
				</div>
            </div>
				
			<div id={'overlay'} className="menu_mobile">
				<ul>
					<li><a href={'/'}>My servers</a></li>
					<li><a href={'/account'}>Profile</a></li>
					{rootAdmin &&
					<li><a href={'/admin'}>Profile</a></li>
					}
					<li><a onClick={onTriggerLogout} href={'#'}>Sign out</a></li>
				</ul>
			</div>
        </Navigation>
    );
};
