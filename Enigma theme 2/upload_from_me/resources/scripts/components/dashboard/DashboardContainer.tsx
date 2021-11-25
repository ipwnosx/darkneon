import React, { useEffect, useState } from 'react';
import { Server } from '@/api/server/getServer';
import getServers from '@/api/getServers';
import ServerRow from '@/components/dashboard/ServerRow';
import Spinner from '@/components/elements/Spinner';
import PageContentBlock from '@/components/elements/PageContentBlock';
import useFlash from '@/plugins/useFlash';
import { useStoreState } from 'easy-peasy';
import { usePersistedState } from '@/plugins/usePersistedState';
import Switch from '@/components/elements/Switch';
import tw from 'twin.macro';
import useSWR from 'swr';
import { PaginatedResult } from '@/api/http';
import Pagination from '@/components/elements/Pagination';
import { useLocation } from 'react-router-dom';

export default () => {
    const { search } = useLocation();
    const defaultPage = Number(new URLSearchParams(search).get('page') || '1');

    const [ page, setPage ] = useState((!isNaN(defaultPage) && defaultPage > 0) ? defaultPage : 1);
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const uuid = useStoreState(state => state.user.data!.uuid);
    const rootAdmin = useStoreState(state => state.user.data!.rootAdmin);
    const [ showOnlyAdmin, setShowOnlyAdmin ] = usePersistedState(`${uuid}:show_all_servers`, false);

    const { data: servers, error } = useSWR<PaginatedResult<Server>>(
        [ '/api/client/servers', (showOnlyAdmin && rootAdmin), page ],
        () => getServers({ page, type: (showOnlyAdmin && rootAdmin) ? 'admin' : undefined }),
    );

    useEffect(() => {
        if (!servers) return;
        if (servers.pagination.currentPage > 1 && !servers.items.length) {
            setPage(1);
        }
    }, [ servers?.pagination.currentPage ]);

    useEffect(() => {
        // Don't use react-router to handle changing this part of the URL, otherwise it
        // triggers a needless re-render. We just want to track this in the URL incase the
        // user refreshes the page.
        window.history.replaceState(null, document.title, `/${page <= 1 ? '' : `?page=${page}`}`);
    }, [ page ]);

    useEffect(() => {
        if (error) clearAndAddHttpError({ key: 'dashboard', error });
        if (!error) clearFlashes('dashboard');
    }, [ error ]);

    return (
	<PageContentBlock title={'Dashboard'} showFlashKey={'dashboard'}>
	<div className="custom_content">
		<div className="user-box first-box">
			<div id={'graphics'} className="activity card">
			<div className="title">New servers</div>
				<canvas id={'chart_1'}></canvas>
			</div>
			<div id={'graphics'} className="activity card">
				<div className="title">New users</div>
				<canvas id={'chart_2'}></canvas>
			</div>
		</div>
			
		<div className="user-box second-box">
			<div className="cards-wrapper">
				<div className="cards-header">
					<div className="cards-view icon">
						<i className="feather feather-calendar fas fa-server fa-2x"></i>
						My servers
					</div>
					<a href={'#'}><div id="create_server" className="cards-button button">
						<i className="fas fa-plus fa-lg"></i>
						Create
					</div></a>
				</div>
				<div className="cards card">
				
					{rootAdmin &&
					<div css={tw`mb-2 flex justify-end items-center`}>
						<p css={tw`uppercase text-xs text-neutral-400 mr-2`}>
							{showOnlyAdmin ? 'Showing others\' servers' : 'Showing your servers'}
						</p>
						<Switch
							name={'show_all_servers'}
							defaultChecked={showOnlyAdmin}
							onChange={() => setShowOnlyAdmin(s => !s)}
						/>
					</div>
					}
					{!servers ?
						<Spinner centered size={'large'}/>
						:
						<Pagination data={servers} onPageSelect={setPage}>
							{({ items }) => (
								items.length > 0 ?
									items.map((server, index) => (
										<ServerRow
											key={server.uuid}
											server={server}
											css={index > 0 ? tw`mt-2` : undefined}
										/>
									))
									:
									<p css={tw`text-center text-sm text-neutral-400`}>
										{showOnlyAdmin ?
											'There are no other servers to display.'
											:
											'There are no servers associated with your account.'
										}
									</p>
							)}
						</Pagination>
					}
				</div>
			</div>
		
			<div className="card transection">
				<div className="transection-header">
					<div className="title icon stat_title">Statistic</div><div className="clear"></div>
					<div className="stats"><button className="stats_box"><i className="fas fa-server"></i></button><h1 id={'server-counter'}></h1><p>All servers</p></div>
					<div className="stats"><button className="stats_box stats_box_2"><i className="fas fa-user"></i></button><h1 id={'users-counter'}></h1><p>All users</p></div>
				</div>
			</div>
		</div>
	</div>
	</PageContentBlock>
    );
};
