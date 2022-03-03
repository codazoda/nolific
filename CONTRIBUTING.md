# Contributing

These are personal notes for my own maintenance of this project. Eventually I'll add notes for outside contributers.

**Working on a Branch**

Create a branch to work on. Run the server locally on a different port to test it. Commit and push the branch as often as you like. When it's ready, merge into main or send a Github PR to do so.

```
git checkout -b your_branch_name
php -S localhost:9000
```

**Tagging**

I'm using semver now. The first two versions didn't properly follow the convention but I'll do so starting with v0.3.0 (which is the third minor release).

major.minor.patch

```
git tag -a v0.3.0 -m "New version"
git push --tags
```

**List commits since last tag**

`git log <yourlasttag>..HEAD --oneline`
