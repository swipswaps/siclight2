<?php
/* Site Info Center LIGHT 2 (SIC LIGHT 2)
 * author: André Herdling (andreherdling.de)
 *
 * see CHANGELOG.md for changes history
 * */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Site Info Center Light 2</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/uikit.min.css" />
    <link rel="stylesheet" href="css/sic_light.css" />
</head>
<body>

    <div class="uk-container">
 
            
            <div id="sic">

                <h1>Site Info Center LIGHT <small>{{ sicVersion }}</small></h1>

                <?php if(!file_exists('sites-config.php')): ?>
                    <!-- error message if sites-config.php does not exist -->
                    <div data-uk-alert class="uk-alert-danger">
                        <strong>sites-config.php not found!</strong> In order to create this file, just rename the sites-config.NEW.php
                    </div>
                <?php endif; ?>  

                <!-- active sites -->
                <div class="uk-card uk-card-default">
                    <div class="uk-card-header">
                        <div uk-grid class="uk-child-width-expand">
                            <div>
                                <h2 class="uk-card-title">Active Sites <span class="uk-badge">{{ objectLength(activeSites) }}</span> <span v-if="objectLength(sortedSites) != objectLength(activeSites)" class="uk-badge filter-count-badge">Filtered: {{ objectLength(sortedSites) }}</span></h2>
                            </div>
                            <div class="header-left">
                                <button @click="refreshAllSites" class="refresh-all uk-button uk-button-danger" type="button" data-uk-tooltip title="Refresh all active sites"><span :data-uk-icon="'icon: refresh'"></span> ALL</button>
                                <button @click="refreshFilteredSites" class="refresh-selected uk-button uk-button-primary" type="button" data-uk-tooltip title="Refresh filtered sites"><span :data-uk-icon="'icon: refresh'"></span> FILTERED</button>
                                <a v-if="summaryUrl != ''" :href="summaryUrl" target="blank" class="uk-button uk-button-default download-summary" data-uk-tooltip title="Download latest summary CSV"><span :data-uk-icon="'icon: download'"></span> CSV</a>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">


                        <div class='uk-grid-small filter-and-search' data-uk-grid>
                            <div class="uk-width-1-3@s uk-width-1-5@m uk-width-1-5@l">
                                <select class="uk-select" @change="sysFilterChange($event)" v-model="systemFilter" id="system-filter">
                                    <option value="">System</option>
                                    <template v-for="(count,system) in activeSitesSystems">
                                        <option :value="system" :key="system">{{ system }} ({{ count }})</option>
                                    </template> 
                                    
                                </select>
                            </div>

                            <div class="uk-width-1-3@s uk-width-2-5@m uk-width-3-5@l">
                                <form class='uk-search uk-search-default'>
                                    <span class='uk-search-icon-flip' uk-search-icon></span>
                                    <input class='uk-search-input' id='search_sites' type='search' placeholder='Search Sites' autocomplete='off' v-model="searchTerm">
                                </form>
                            </div>

                            <div class='uk-width-1-3@s uk-width-2-5@m uk-width-1-5@l'>
                              <button @click="resetSearchAndFilter" uk-filter-control id='resetFilterAndSearch' class='uk-button uk-button-default'>Reset filter &amp; search</button>
                            </div>    

                        </div>



                        <table class='sites uk-table uk-table-divider'>
                            <thead>
                                <tr>
                                    <th @click="sort('name')" class="sortable" :class="{ 'sort': currentSort === 'name' , 'sort-desc': currentSort === 'name' && currentSortDir === 'desc' }">Name</th>
                                    <th @click="sort('sys')" class="sortable" :class="{ 'sort': currentSort === 'sys' , 'sort-desc': currentSort === 'sys' && currentSortDir === 'desc' }">System</th>
                                    <th @click="sort('sys_ver')" class="sortable" :class="{ 'sort': currentSort === 'sys_ver' , 'sort-desc': currentSort === 'sys_ver' && currentSortDir === 'desc' }">Sys Ver</th>
                                    <th @click="sort('php_ver')" class="sortable" :class="{ 'sort': currentSort === 'php_ver' , 'sort-desc': currentSort === 'php_ver' && currentSortDir === 'desc' }">PHP Ver</th>
                                    <th @click="sort('sat_ver')" class="sortable" :class="{ 'sort': currentSort === 'sat_ver' , 'sort-desc': currentSort === 'sat_ver' && currentSortDir === 'desc' }">Sat Ver</th>
                                    <th>Refreshed</th>
                                    <td>&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody class="js-filter">
                                <template>
                                    <tr class="active_site" :class="site.state" v-for="(site,key) in sortedSites" :key="site.hash">
                                        <td>{{ site.name }}</td>
                                        <td>{{ site.sys }}</td>
                                        <td class="sys_ver">{{ site.sys_ver }}</td>
                                        <td class="php_ver">{{ site.php_ver }}</td>
                                        <td class="sat_ver">{{ site.sat_ver }}</td>
                                        <td class="time">{{ site.date }}<template v-if="site.time !== ''">, </template>{{ site.time }}</td>
                                        <td class="actions">
                                            <span uk-lightbox><a v-if="site.history" class="history uk-button uk-button-default" :href="site.history" data-type="iframe" data-uk-tooltip title="Show history"><span :uk-icon="'icon: clock'"></span></a></span>
                                            <button @click="refreshSingleSite" :data-id="site.hash" :data-name="site.name" class="refresh uk-button uk-button-primary" type="button" data-uk-tooltip title="Refresh"><span uk-icon="icon: refresh"></span></button>
                                        </td>
                                    </tr>
                                </template>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /active sites -->


                <!-- inactive sites -->
                <div class="uk-card uk-card-default inactivesites">
                    <div class="uk-card-header">
                        <div uk-grid class="uk-child-width-expand">
                            <div>
                                <h2 class="uk-card-title">Inactive Sites <span class="uk-badge">{{ objectLength(inactiveSites) }}</span> <small>(still not or no longer maintained)</small></h2>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                            <table class='sites uk-table uk-table-divider'>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>System</th>
                                        <th>Sys Ver</th>
                                        <th>PHP Ver</th>
                                        <th>Sat Ver</th>
                                        <th>Refreshed</th>
                                        <td>&nbsp;</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template>
                                        <tr class="inactive_site" :data-id="key" :data-name="site.name" :data-sys="site.sys" v-for="(site,key) in inactiveSites">
                                            <td>{{ site.name }}</td>
                                            <td>{{ site.sys }}</td>
                                            <td class="sys_ver">{{ site.sys_ver }}</td>
                                            <td class="php_ver">{{ site.php_ver }}</td>
                                            <td class="sat_ver">{{ site.sat_ver }}</td>
                                            <td class="time">{{ site.date }}<template v-if="site.time !== ''">, </template>{{ site.time }}</td>
                                            <td class="actions">
                                                <span uk-lightbox><a v-if="site.history" class="history uk-button uk-button-default" :href="site.history" data-type="iframe" data-uk-tooltip title="Show history"><span :uk-icon="'icon: clock'"></span></a></span>
                                            </td>
                                        </tr>
                                    </template>
                                </body>    
                            </table>
                    </div>
                </div>
                <!-- /inactive sites -->

                <div id="progress">
                    <progress id="progressbar" class="uk-progress" :value="progressDone" :max="progressMax"></progress>
                </div>


            </div>
            <!-- /#sic -->


        

        <div class="licenses">
            SIC LIGHT by <a href="https://www.andreherdling.de">André Herdling</a> | <a href="licenses.txt">Licenses &amp; used software</a>
        </div>     
    </div>
 
    

    

    
    <script src="js/vue.js"></script>
    <script src="js/axios.min.js"></script>
    <script src="js/uikit.min.js"></script>
    <script src="js/uikit-icons.min.js"></script>
    <script src="js/main.js"></script>
    
</body>
</html>
