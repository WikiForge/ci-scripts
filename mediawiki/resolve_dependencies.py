# A script to resolve dependencies of MediaWiki extension for Quibble test
import yaml

# pf for https://raw.githubusercontent.com/wikimedia/integration-config/master/zuul/parameter_functions.py
from pf import dependencies, get_dependencies

# Add dependencies of target extension
with open('dependencies', 'r') as f:
    dependencies['ext'] = yaml.load(f, Loader=yaml.SafeLoader)

# Resolve
resolvedDependencies = []
for d in get_dependencies('ext', dependencies):
  repo = ''
  branch = ''
  if d in dependencies['ext'] and 'repo' in dependencies['ext'][d]:
    if dependencies['ext'][d]['repo'] != 'auto':
      repo = '|' + dependencies['ext'][d]['repo']
    if 'branch' in dependencies['ext'][d]:
      if dependencies['ext'][d]['branch'] != 'auto':
        branch = '|' + dependencies['ext'][d]['branch']

  # Skip parsoid which is a virtual extension
  if d == 'parsoid':
    continue
  d = 'mediawiki/extensions/' + d
  d = d.replace('/extensions/skins/', '/skins/')
  d = d + repo + branch
  resolvedDependencies.append(d)
print(' '.join(resolvedDependencies))
