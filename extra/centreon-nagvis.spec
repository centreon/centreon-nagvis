%define debug_package %{nil}
Name:           centreon-nagvis
Version:        1.0.3
Release:        1%{?dist}
Summary:        Centreon Nagvis
Group:          System Environment/Base
License:        GPLv2
URL:            http://forge.centreon.com/projects/centreon-nagvis
Source0:        http://192.168.1.152/%{name}/%{name}-%{version}.tar.gz
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
BuildArch:      noarch
BuildRequires:  centreon-devel
BuildRequires:  dos2unix
Requires:       centreon >= 2.4

%description
Display the NagVis maps into Centreon

%prep
%setup -n %{name}-%{version}

find .          \
        -type f \
        -exec %{__grep} -qE '(/etc/centreon/)|(/usr/bin)|(CENTREON_DIR)|(CENTREON_LOG)|(MODULE_NAME)|(DB_CENTSTORAGE)|(CENTREON_AUTODISCO_PATH)' {} ';'   \
        -exec %{__sed} -i -e "s|/etc/centreon/|%{centreon_etc}/|g" \
                          -e "s|/usr/bin|%{_bindir}|g" \
                          -e "s|/usr/share/centreon|%{centreon_dir}|g" \
                          -e "s|/var/log/centreon|%{centreon_log}|g" \
                          -e "s|centreon-nagvis|%{name}|g" \
                          -e "s|centreon_storage|centreon_storage|g" \
                          -e "s|/usr/share/centreon/www/modules/centreon-nagvis/|%{centreon_www}/modules/%{name}/|g" \
                       {} ';'

%build

%install

rm -rf $RPM_BUILD_ROOT

%{__install} -d $RPM_BUILD_ROOT%{centreon_www}/modules/%{name}
%{__cp} -rp www/modules/%{name}/* $RPM_BUILD_ROOT%{centreon_www}/modules/%{name}

%clean
rm -rf $RPM_BUILD_ROOT

##################################################
%files

%defattr(-,root,root,-)
# %doc www/CHANGELOG

%attr(-,apache,apache)
%{centreon_www}/modules/%{name}

##################################################
%changelog
* Fri Dec  5 2014 Quentin Delance <qdelance@merethis.com> 1.0.3-1
- Version update before publishing this module on public forge
- Bump version in DB to match RPM version
- Change default NagVis PATH to match documentation

* Wed Oct 16 2013 Maximilien Bersoult <mbersoult@merethis.com> 1.0.1-1
- Fix problem when display pages

* Thu Oct 10 2013 Julien Mathis <jmathis@merethis.com> 1.0.0-1
- Initial version
